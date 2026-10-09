<?php
/**
 * XlsReader - minimal, dependency-free reader for legacy Excel 97-2003 (.xls / BIFF8).
 *
 * Reads only the FIRST worksheet and returns cell text. Supports the cell
 * types attendance machines normally write: LABEL, LABELSST (shared strings),
 * NUMBER, RK and MULRK. Formatting, formulas and merged cells are ignored.
 *
 * Usage:  $rows = XlsReader::readFirstSheet($path);   // [rowIndex][colIndex] => string
 *         throws RuntimeException on an unreadable file.
 */
class XlsReader
{
    private $d;          // raw file bytes
    private $fat = [];
    private $sectorSize;
    private $miniSectorSize;
    private $miniCutoff;

    public static function readFirstSheet($path)
    {
        $r = new self();
        return $r->parse($path);
    }

    private function parse($path)
    {
        $this->d = @file_get_contents($path);
        if ($this->d === false || strlen($this->d) < 512) {
            throw new RuntimeException('Cannot read the Excel file.');
        }
        if (substr($this->d, 0, 8) !== "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
            throw new RuntimeException('Not a valid .xls file (if it is .xlsx, please save it as .xls or .csv).');
        }
        $wb = $this->readOleStream('Workbook');
        if ($wb === null) $wb = $this->readOleStream('Book');
        if ($wb === null) throw new RuntimeException('Workbook data not found in the .xls file.');
        return $this->parseBiff($wb);
    }

    // ---------- OLE2 compound file ----------
    private function u16($s, $o) { return unpack('v', substr($s, $o, 2))[1]; }
    private function u32($s, $o) { return unpack('V', substr($s, $o, 4))[1]; }

    private function sector($i)
    {
        return substr($this->d, 512 + $i * $this->sectorSize, $this->sectorSize);
    }

    private function chain($start, $table)
    {
        $out = [];
        $s = $start;
        $guard = 0;
        while ($s < 0xFFFFFFF0 && isset($table[$s]) && $guard++ < 1000000) {
            $out[] = $s;
            $s = $table[$s];
        }
        return $out;
    }

    private function readOleStream($wanted)
    {
        $h = $this->d;
        $this->sectorSize     = 1 << $this->u16($h, 30);
        $this->miniSectorSize = 1 << $this->u16($h, 32);
        $this->miniCutoff     = $this->u32($h, 56);
        $dirStart   = $this->u32($h, 48);
        $miniFatStart = $this->u32($h, 60);
        $difatStart = $this->u32($h, 68);

        // Collect FAT sector ids (109 in header, rest through DIFAT chain)
        $fatSectors = [];
        for ($i = 0; $i < 109; $i++) {
            $s = $this->u32($h, 76 + 4 * $i);
            if ($s < 0xFFFFFFF0) $fatSectors[] = $s;
        }
        $perSector = $this->sectorSize / 4;
        $d = $difatStart;
        $guard = 0;
        while ($d < 0xFFFFFFF0 && $guard++ < 10000) {
            $buf = $this->sector($d);
            for ($i = 0; $i < $perSector - 1; $i++) {
                $s = $this->u32($buf, 4 * $i);
                if ($s < 0xFFFFFFF0) $fatSectors[] = $s;
            }
            $d = $this->u32($buf, 4 * ($perSector - 1));
        }
        $this->fat = [];
        foreach ($fatSectors as $fs) {
            $buf = $this->sector($fs);
            for ($i = 0; $i < $perSector; $i++) $this->fat[] = $this->u32($buf, 4 * $i);
        }

        // Directory
        $dir = '';
        foreach ($this->chain($dirStart, $this->fat) as $s) $dir .= $this->sector($s);
        $root = null; $target = null;
        for ($i = 0; $i + 128 <= strlen($dir); $i += 128) {
            $e = substr($dir, $i, 128);
            $nl = $this->u16($e, 64);
            if ($nl < 2) continue;
            $name = @iconv('UTF-16LE', 'UTF-8//IGNORE', substr($e, 0, $nl - 2));
            $type = ord($e[66]);
            $entry = ['start' => $this->u32($e, 116), 'size' => $this->u32($e, 120)];
            if ($type === 5) $root = $entry;
            if ($type === 2 && $name === $wanted && $target === null) $target = $entry;
        }
        if ($target === null) return null;

        // Big stream (regular sectors)
        if ($target['size'] >= $this->miniCutoff || $root === null) {
            $out = '';
            foreach ($this->chain($target['start'], $this->fat) as $s) $out .= $this->sector($s);
            return substr($out, 0, $target['size']);
        }

        // Small stream (mini sectors, stored inside the root entry's stream)
        $miniFat = [];
        foreach ($this->chain($miniFatStart, $this->fat) as $s) {
            $buf = $this->sector($s);
            for ($i = 0; $i < $perSector; $i++) $miniFat[] = $this->u32($buf, 4 * $i);
        }
        $miniStream = '';
        foreach ($this->chain($root['start'], $this->fat) as $s) $miniStream .= $this->sector($s);
        $out = '';
        foreach ($this->chain($target['start'], $miniFat) as $s) {
            $out .= substr($miniStream, $s * $this->miniSectorSize, $this->miniSectorSize);
        }
        return substr($out, 0, $target['size']);
    }

    // ---------- BIFF8 ----------
    private function parseBiff($wb)
    {
        // Split into records; fold CONTINUE (0x3C) into the previous record's data,
        // remembering where each continuation starts (needed for shared strings).
        $recs = [];
        $p = 0; $n = strlen($wb);
        while ($p + 4 <= $n) {
            $t = $this->u16($wb, $p);
            $l = $this->u16($wb, $p + 2);
            $data = substr($wb, $p + 4, $l);
            $p += 4 + $l;
            if ($t === 0x3C && !empty($recs)) {
                $last = count($recs) - 1;
                $recs[$last]['cont'][] = strlen($recs[$last]['data']);
                $recs[$last]['data'] .= $data;
            } else {
                $recs[] = ['t' => $t, 'data' => $data, 'cont' => []];
            }
        }

        $sst = [];
        $rows = [];
        $bofCount = 0;
        $inFirstSheet = false;
        foreach ($recs as $r) {
            $t = $r['t']; $x = $r['data'];
            if ($t === 0x00FC) {                       // SST
                $sst = $this->parseSst($x, $r['cont']);
            } elseif ($t === 0x0809) {                 // BOF
                $bofCount++;
                $type = strlen($x) >= 4 ? $this->u16($x, 2) : 0;
                // first worksheet BOF (type 0x0010) after the workbook globals
                if ($type === 0x0010 && !$inFirstSheet && empty($rows)) $inFirstSheet = true;
            } elseif ($t === 0x000A) {                 // EOF
                if ($inFirstSheet) break;
            } elseif ($inFirstSheet) {
                if ($t === 0x0204 && strlen($x) >= 8) {            // LABEL
                    $row = $this->u16($x, 0); $col = $this->u16($x, 2);
                    $rows[$row][$col] = $this->readXlString($x, 6, true)[0];
                } elseif ($t === 0x00FD && strlen($x) >= 10) {     // LABELSST
                    $row = $this->u16($x, 0); $col = $this->u16($x, 2);
                    $idx = $this->u32($x, 6);
                    $rows[$row][$col] = $sst[$idx] ?? '';
                } elseif ($t === 0x0203 && strlen($x) >= 14) {     // NUMBER
                    $row = $this->u16($x, 0); $col = $this->u16($x, 2);
                    $rows[$row][$col] = $this->numToStr(unpack('d', substr($x, 6, 8))[1]);
                } elseif ($t === 0x027E && strlen($x) >= 10) {     // RK
                    $row = $this->u16($x, 0); $col = $this->u16($x, 2);
                    $rows[$row][$col] = $this->numToStr($this->decodeRk($this->u32($x, 6)));
                } elseif ($t === 0x00BD && strlen($x) >= 6) {      // MULRK
                    $row = $this->u16($x, 0); $col = $this->u16($x, 2);
                    $last = $this->u16($x, strlen($x) - 2);
                    $o = 4;
                    for ($c = $col; $c <= $last && $o + 6 <= strlen($x) - 2; $c++, $o += 6) {
                        $rows[$row][$c] = $this->numToStr($this->decodeRk($this->u32($x, $o + 2)));
                    }
                }
            }
        }
        ksort($rows);
        foreach ($rows as &$cols) ksort($cols);
        return $rows;
    }

    private function numToStr($v)
    {
        if (floor($v) == $v && abs($v) < 1e15) return (string)(int)$v;
        return rtrim(rtrim(sprintf('%.10F', $v), '0'), '.');
    }

    private function decodeRk($rk)
    {
        $mult = ($rk & 1) ? 0.01 : 1;
        if ($rk & 2) {
            $v = $rk >> 2;
            if ($v & 0x20000000) $v -= 0x40000000;   // sign-extend 30-bit int
            return $v * $mult;
        }
        $bin = pack('V', 0) . pack('V', $rk & 0xFFFFFFFC);
        return unpack('d', $bin)[0] * $mult;
    }

    /** Parse an XLUnicodeString at $o. Returns [string, nextOffset]. Does not handle CONTINUE. */
    private function readXlString($x, $o, $len16)
    {
        if ($len16) { $cch = $this->u16($x, $o); $o += 2; }
        else        { $cch = ord($x[$o]);        $o += 1; }
        $flags = ord($x[$o]); $o++;
        $wide = ($flags & 0x01) !== 0;
        $rich = ($flags & 0x08) !== 0;
        $ext  = ($flags & 0x04) !== 0;
        $runs = 0; $extLen = 0;
        if ($rich) { $runs = $this->u16($x, $o); $o += 2; }
        if ($ext)  { $extLen = $this->u32($x, $o); $o += 4; }
        $bytes = $wide ? $cch * 2 : $cch;
        $raw = substr($x, $o, $bytes);
        $o += $bytes + $runs * 4 + $extLen;
        return [$this->decodeChars($raw, $wide), $o];
    }

    private function decodeChars($raw, $wide)
    {
        if ($wide) return (string)@iconv('UTF-16LE', 'UTF-8//IGNORE', $raw);
        return (string)@iconv('Windows-1252', 'UTF-8//IGNORE', $raw);
    }

    /** Shared string table; handles strings split across CONTINUE boundaries. */
    private function parseSst($x, $cont)
    {
        $total = strlen($x);
        $bounds = $cont;                       // offsets where a CONTINUE chunk begins
        $o = 8;                                // skip total/unique counts
        $unique = $this->u32($x, 4);
        $out = [];
        for ($i = 0; $i < $unique && $o + 3 <= $total; $i++) {
            $cch = $this->u16($x, $o); $o += 2;
            $flags = ord($x[$o]); $o++;
            $wide = ($flags & 0x01) !== 0;
            $rich = ($flags & 0x08) !== 0;
            $ext  = ($flags & 0x04) !== 0;
            $runs = 0; $extLen = 0;
            if ($rich) { $runs = $this->u16($x, $o); $o += 2; }
            if ($ext)  { $extLen = $this->u32($x, $o); $o += 4; }
            $str = '';
            $remaining = $cch;
            while ($remaining > 0 && $o < $total) {
                // end of the current chunk = next boundary after $o (or end of data)
                $chunkEnd = $total;
                foreach ($bounds as $b) { if ($b > $o) { $chunkEnd = $b; break; } }
                $bytesPer = $wide ? 2 : 1;
                $take = min($remaining, intdiv($chunkEnd - $o, $bytesPer));
                $str .= $this->decodeChars(substr($x, $o, $take * $bytesPer), $wide);
                $o += $take * $bytesPer;
                $remaining -= $take;
                if ($remaining > 0 && $o >= $chunkEnd && $o < $total) {
                    // continuation chunk starts with a 1-byte flags field for the rest of the string
                    $wide = (ord($x[$o]) & 0x01) !== 0;
                    $o++;
                }
            }
            $o += $runs * 4 + $extLen;
            $out[] = $str;
        }
        return $out;
    }
}
