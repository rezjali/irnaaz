<?php

/*	Jalali to Gregorian and Gregorian to Jalali Convertor
	Copyright (C) 2020 Programming by Omid Milani Moozar
	E-mail: omid.202m@gmail.com
    This version is patched for better error handling and full PHP date() compatibility.
*/

function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
{
    if ($time_zone != 'local') date_default_timezone_set(($time_zone === '') ? 'Asia/Tehran' : $time_zone);
    $ts = ($timestamp === '') ? time() : tr_num($timestamp, 'en');
    $date = explode('_', date('Y_m_d_H_i_s_w', $ts));
    list($g_y, $g_m, $g_d, $h, $i, $s, $w) = $date;
    list($j_y, $j_m, $j_d) = gregorian_to_jalali($g_y, $g_m, $g_d);

    $weekday = array('یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه');
    $month = array('فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');
    
    $output = '';
    for ($i = 0; $i < strlen($format); $i++) {
        $char = $format[$i];
        if ($char === '\\') {
            $output .= $format[++$i];
            continue;
        }
        switch ($char) {
            case 'd': $output .= str_pad($j_d, 2, '0', STR_PAD_LEFT); break;
            case 'D': $output .= substr($weekday[$w], 0, 2); break;
            case 'j': $output .= $j_d; break;
            case 'l': $output .= $weekday[$w]; break;
            case 'N': $output .= $w + 1; break;
            case 'w': $output .= $w; break;
            case 'z': $output .= ($j_m > 6) ? (($j_m - 7) * 30) + $j_d + 186 : (($j_m - 1) * 31) + $j_d -1; break;
            case 'F': $output .= $month[$j_m - 1]; break;
            case 'm': $output .= str_pad($j_m, 2, '0', STR_PAD_LEFT); break;
            case 'M': $output .= substr($month[$j_m - 1], 0, 3); break;
            case 'n': $output .= $j_m; break;
            case 't': $output .= ($j_m == 12 && !jdate('L', $ts, '', $time_zone, 'en')) ? 29 : (($j_m > 6) ? 30 : 31); break;
            case 'L': $output .= (jdate('Y', $ts, '', $time_zone, 'en') % 33 % 4 == 1) ? 1 : 0; break;
            case 'o':
            case 'Y': $output .= $j_y; break;
            case 'y': $output .= substr($j_y, 2, 2); break;
            case 'a': $output .= ($h < 12) ? 'am' : 'pm'; break;
            case 'A': $output .= ($h < 12) ? 'AM' : 'PM'; break;
            case 'g': $output .= ($h % 12 == 0) ? 12 : $h % 12; break;
            case 'G': $output .= $h; break;
            case 'h': $output .= str_pad((($h % 12 == 0) ? 12 : $h % 12), 2, '0', STR_PAD_LEFT); break;
            case 'H': $output .= $h; break;
            case 'i': $output .= $i; break;
            case 's': $output .= $s; break;
            case 'U': $output .= $ts; break;
            default: $output .= $char;
        }
    }
    return ($tr_num === 'fa') ? tr_num($output) : $output;
}

function jstrftime($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
{
	return jdate($format, $timestamp, $none, $time_zone, $tr_num);
}

function jmktime($h = '', $m = '', $s = '', $jm = '', $jd = '', $jy = '', $none = '', $timezone = 'Asia/Tehran')
{
	if ($timezone != 'local') date_default_timezone_set($timezone);
	if ($h === '') {
		return time();
	} else {
		list($h, $m, $s, $jm, $jd, $jy) = explode('_', tr_num($h . '_' . $m . '_' . $s . '_' . $jm . '_' . $jd . '_' . $jy));
		if ($m === '') {
			return mktime($h);
		} else {
			if ($s === '') {
				return mktime($h, $m);
			} else {
				if ($jm === '') {
					return mktime($h, $m, $s);
				} else {
					list($g_y, $g_m, $g_d) = jalali_to_gregorian($jy, $jm, $jd);
					return mktime($h, $m, $s, $g_m, $g_d, $g_y);
				}
			}
		}
	}
}

function jgetdate($timestamp = '', $none = '', $timezone = 'Asia/Tehran', $tn = 'en')
{
	$ts = ($timestamp == '') ? time() : tr_num($timestamp);
	$jdate = jdate('Y_j_n_w_l_M_F_H_i_s', $ts, '', $timezone, $tn);
	$info = explode('_', $jdate);
	return array(
		'seconds' => tr_num((int)($info[9] ?? 0)),
		'minutes' => tr_num((int)($info[8] ?? 0)),
		'hours' => tr_num((int)($info[7] ?? 0)),
		'mday' => tr_num((int)($info[1] ?? 0)),
		'wday' => tr_num((int)($info[3] ?? 0)),
		'mon' => tr_num((int)($info[2] ?? 0)),
		'year' => tr_num($info[0] ?? ''),
		'yday' => jdate('z', $ts, '', $timezone, $tn),
		'weekday' => $info[4] ?? '',
		'month' => $info[6] ?? '',
		0 => tr_num($ts)
	);
}

function jcheckdate($jm, $jd, $jy)
{
	list($jm, $jd, $jy) = explode('_', tr_num($jm . '_' . $jd . '_' . $jy));
	$j_y_en = (int)tr_num($jy);
	$kab = (((($j_y_en % 33) % 4) - 1) == ((int)(($j_y_en % 33) * 0.05))) ? 1 : 0;
	if ($jm > 12 or $jd > 31 or $jm < 1 or $jd < 1) {
		return false;
	} elseif (($jm > 6 and $jd > 30) or ($jm == 12 and $jd > 29 and $kab == 0) or ($jm == 12 and $jd > 30 and $kab == 1)) {
		return false;
	} else {
		return true;
	}
}

function tr_num($str, $mod = 'fa', $mf = '٫') {
	$num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
	$num_b = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $mf);
	return ($mod == 'fa') ? str_replace($num_a, $num_b, $str) : str_replace($num_b, $num_a, $str);
}

function gregorian_to_jalali($g_y, $g_m, $g_d) {
	$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
	$gy = $g_y - 1600; $gm = $g_m - 1; $gd = $g_d - 1;
	$g_day_no = 365 * $gy + floor(($gy + 3) / 4) - floor(($gy + 99) / 100) + floor(($gy + 399) / 400);
	for ($i = 0; $i < $gm; ++$i) $g_day_no += $g_days_in_month[$i];
	if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) $g_day_no++;
	$g_day_no += $gd; $j_day_no = $g_day_no - 79; $j_np = floor($j_day_no / 12053);
	$j_day_no %= 12053; $jy = 979 + 33 * $j_np + 4 * floor($j_day_no / 1461);
	$j_day_no %= 1461;
	if ($j_day_no >= 366) { $jy += floor(($j_day_no - 1) / 365); $j_day_no = ($j_day_no - 1) % 365; }
	for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) $j_day_no -= $j_days_in_month[$i];
	$jm = $i + 1; $jd = $j_day_no + 1;
	return array($jy, $jm, $jd);
}

function jalali_to_gregorian($j_y, $j_m, $j_d)
{
	$j_y = (int)tr_num($j_y, 'en');
	$j_m = (int)tr_num($j_m, 'en');
	$j_d = (int)tr_num($j_d, 'en');
	$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
	$jy = $j_y - 979;
	$jm = $j_m - 1;
	$jd = $j_d - 1;
	$j_day_no = 365 * $jy + floor($jy / 33) * 8 + floor((($jy % 33) + 3) / 4);
	for ($i = 0; $i < $jm; ++$i)
		$j_day_no += $j_days_in_month[$i];
	$j_day_no += $jd;
	$g_day_no = $j_day_no + 79;
	$gy = 1600 + 400 * floor($g_day_no / 146097);
	$g_day_no %= 146097;
	$leap = 1;
	if ($g_day_no >= 36525) {
		$g_day_no--;
		$gy += 100 * floor($g_day_no / 36524);
		$g_day_no %= 36524;
		if ($g_day_no >= 365)
			$g_day_no++;
		else
			$leap = 0;
	}
	$gy += 4 * floor($g_day_no / 1461);
	$g_day_no %= 1461;
	if ($g_day_no >= 366) {
		$leap = 0;
		$g_day_no--;
		$gy += floor($g_day_no / 365);
		$g_day_no %= 365;
	}
	for ($i = 0; $g_day_no >= ($g_days_in_month = array(31, ($leap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31))[$i]; $i++)
		$g_day_no -= $g_days_in_month[$i];
	$gm = $i + 1;
	$gd = $g_day_no + 1;
	return array($gy, $gm, $gd);
}
