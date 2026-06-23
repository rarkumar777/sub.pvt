<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                    ///
 * @link		    www.cms.gogies.net                             ///
 * @Version         1.0                                                   /// 
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies')) {
	print 'Direct script access is not allowed';
	exit;
}
include_once $GOGIES['path'] . '/modules/tours/lang/' . $GOGIES['lang'] . '/lang_data.php';
include_once $GOGIES['path'] . '/config/modules/tours_settings.php';
class Tours
{
	///////////////////////////
	function date_in_range($s, $e)
	{
		$s = str_replace('-', '', $s);
		$e = str_replace('-', '', $e);
		$current_date = date('Y') . date('m') . date('d');
		if ($current_date >= $s && $current_date <= $e) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	///////////
	function get_total($price, $count)
	{
		global $G;
		$t = ($price * $count);
		///////////rate conversation
		// $t=$G['invoice']->convert_currency($t);
		return $t;
	}
	///////////////////////////
	function check_booking_date($d)
	{
		$today = new DateTime();
		$bday = new DateTime($d);
		$dif = $today->diff($bday);
		$dif = $dif->format('%R%a');
		if (1 > $dif) {
			return FALSE;
		} elseif (730 < $dif) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	///////////////////////
	function get_tour_tec_rates($arr, $arr2)
	{
		global $G;
		$tec_array = $arr2;
		$tour_tec = @unserialize($arr, ['allowed_classes' => FALSE]);
		if (!is_array($tour_tec['enable'])) {
			$tour_tec['enable'] = [];
		}
		$tour_tec_details = NULL;
		$tec_count = 0;
		foreach ($tour_tec['enable'] as $tec_stats) {
				if ($tec_count < 3) {
						if (isset($tec_array[$tec_stats])) {
								$x = NULL;
								$tec_count = $tec_count + 1;
								if ($tec_count > 2) {
									$x = 'hide-md';
								}
								$tour_tec_details .= '<div class="row" ><div class="sd-6 pad-l pad-t"><span class="small" data-truncate="1"> ' . $tec_array[$tec_stats]['name'] . '</span></div><div class="sd-6 pad-t">' .
									$G['common']->get_item_rating($tour_tec['rates'][$tec_stats], $tec_array[$tec_stats]['icon']) . '</div></div>';
							}
					}
			}
		return $tour_tec_details;
	}
	////////////////////////
	function get_max_price($bases)
	{
		$bases = unserialize($bases, ['allowed_classes' => FALSE]);
		if (!is_array($bases) or empty($bases)) {
			return NULL;
		} else {
				$numbers = array_column($bases, 'price');
				$to = max($numbers);
				return $to;
			}
	}
	////////////////////////
	////////////////////////
	function get_min_price($groups, $bases)
	{
		$from = false;
		$groups = unserialize($groups, ['allowed_classes' => FALSE]);

		if (is_array($groups)) {
				foreach ($groups as $k => $v) {
						if (is_array($v)) {
								$numbers = array_column($v, 'adult');
								$from[] = min($numbers);
							}
					}
				if (is_array($from)) {
					$from = min($from);
				}
			}

		if (!$from) {
				$bases = unserialize($bases, ['allowed_classes' => FALSE]);
				$numbers = array_column($bases, 'price');
				$from = min($numbers);
			}
		if (!$from) {
				$from = NULL;
			}
		return $from;
	}
	////////////////////////
	function update_price_margin($id, $bases, $bases_low, $bases_high, $gourps, $groups_low, $groups_high)
	{
		global $GOGIES, $G;
		$max[] = $this->get_max_price($bases);
		$max[] = $this->get_max_price($bases_low);
		$max[] = $this->get_max_price($bases_high);
		$max_key = array_search(max($max), $max);

		$min[] = $this->get_min_price($groups_low, $bases_low);
		$min[] = $this->get_min_price($groups_high, $bases_high);
		$min[] = $this->get_min_price($gourps, $bases);
		$min_key = array_search(min($min), $min);

		$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'tours SET min_price=\'' . $min[$min_key] . '\', max_price=\'' . $max[$max_key] . '\' WHERE id=\'' . $id . '\' LIMIT 1');
	}
	//////////////////////////////
	////////////////////////
	function get_tour_pricing($min, $max, $id, $start_extra = NULL)
	{
		global $lang, $GOGIES, $Q, $G;
		$price = NULL;
		if ($min > 0.01) {
			$price = $G['invoice']->convert_currency($min);
			if ($min != $max) {
				$price .= ' - ' . $G['invoice']->convert_currency($max);
			}
			$price .= ' ' . $GOGIES['currencies'][$GOGIES['currency']]['symbol'];
			$price = $start_extra . ' ' . $price;
		} else {
			$price = '<a class=" h-pad-l h-pad-r text-capitalize" href="' . $GOGIES['url'] . '/' . $Q[0] . '/tours/inquery/' . $id . '/"><i class="fa-bar-chart-o"></i> ' . $lang['get_price_quote'] . '</a>';
		}
		return $price;
	}
	//////////////////////////////
	function get_travelers_pricing($travelers_count, $base_pricing, $base)
	{
		if (is_array($base_pricing)) {
			ksort($base_pricing);
		} else {
			$base_pricing = [];
		}
		$pricing = FALSE;
		foreach ($base_pricing as $k => $v) {

				if ($travelers_count >= $k) {
					$pricing = $v;
				}
			}
		if (!$pricing) {
			$pricing['adult'] = $base['price'];
			$pricing['child'] = $base['price'];
			$pricing['infant'] = $base['price'];
		}
		$pricing['single_supplement'] = $base['single_supplement'];
		//print_r ($pricing); exit;
		return $pricing;
	}
	///////////////////////////////
	////////////////
	function calculate_tax($total)
	{
		global $GOGIES;
		return ($GOGIES['tours_settings']['tax'] / 100) * $total;
	}
	////////////////	date BETWEEN '" . $from_date . "' AND  '" . $to_date . "'
	function check_season_date($id, $date)
	{
		global $G, $GOGIES;
		$date = $G['security']->db_escape($date);
		$id = intval($id);
		//$sql=$G['db']->q('SELECT count(id) from '.$GOGIES['dbprf'].'tours_seasons where \''.$date.'\' >= from_date and \''.$date.'\' <= to_date AND tour_id=\''.$id.'\'' );
		$sql = $G['db']->q('SELECT count(id) from ' . $GOGIES['dbprf'] . 'tours_seasons where \'' . $date . '\' BETWEEN `from_date` AND `to_date` AND tour_id=\'' . $id . '\'');
		$count = mysqli_fetch_array($sql);
		$count = $count[0];
		if ($count > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/////////////////

	//////////////////////////
	function get_expenses_fast_access_tree($elements, $parentId = 0, $country_id)
	{
		global $lang, $G, $GOGIES;
		if (!isset($branch)) {
			$branch = NULL;
		}

		foreach ($elements as $k => $v) {
			if (isset($_POST['country_' . $country_id . '_' . $k]) && !isset($GOGIES['already_definded_fast_access'][$k])) {
					$GOGIES['already_definded_fast_access'][$k] = 1;
					$GOGIES['fast_expenses_save_data'] .= ' $GOGIES[\'fast_expenses\'][' . $country_id . '][' . $k . ']=' . $k . '; ';
				}
			if ($v['parent_id'] == $parentId) {
				$branch .= '<li>' . $G['form']->addInput([
					'type' => 'checkbox',
					'attr' => ['value' => '1', 'name' => 'country_' . $_GET['country'] . '_' . $k]
				]) . '<label for="country_' . $_GET['country'] . '_' . $k . '">' . $v['name'] . '</label>';
				$children = $this->get_expenses_fast_access_tree($elements, $k, $country_id);
				if ($children) {
					$branch .= '<ul>' . $children . '</ul>';
				}

				//$branch[$k] = $element;
				$branch .= '</li>';
				unset($elements[$k]);
			}
		}

		return '<ul class="treeview">' . $branch . '</ul>';
	}

	/////////////////////////

	/////////////////////
	function get_expenses_fast_access($elements, $country_id, $parentId = 0)
	{
		global $lang;
		if (!isset($branch)) {
			$branch = NULL;
		}

		foreach ($elements as $k => $v) {
			if ($v['parent_id'] == $parentId) {
				$branch .= '<li><a href="javascript:void(0);" data-category-id="' . $k . '" data-country="' . $country_id . '">' . $v['name'] . '</a></li>';


				unset($elements[$k]);
			}
			if ($k == $parentId) {
				$categ_name = $v['name'];
			}
		}
		if (!empty($branch)) {
			$branch = '<span class="dropdown">
<span class="small h-pad btn blue" id="toggler">' . $categ_name . '</span>
<ul>
' . $branch . '
</ul>
</span>';
		} else {
			$branch = ' <a href="javascript:void(0);" class="btn blue small h-pad" data-category-id="' . $parentId . '"  data-country="' . $country_id . '">' . $categ_name . '</a> ';
		}
		return $branch;
	}
	/////////////////////////////////////////////
	function gen_traveler_fields($id, $room_id = 0, $input_extra = NULL)
	{
		global $G, $lang;
		$data = '<div class="row cell">';
		$data .= '<div class="md-2">' .
			$G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['name'], 'title' => $lang['name'], 'name' => 'traveler_name_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-1">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['passport_no'], 'title' => $lang['passport_no'], 'name' => 'traveler_passport_number_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-2">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['issue_date'], 'title' => $lang['issue_date'], 'data-view-mode' => 'years', 'class' => 'datepicker', 'name' => 'traveler_passport_issue_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-2">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['expire_date'], 'title' => $lang['expire_date'], 'data-view-mode' => 'years', 'class' => 'datepicker', 'name' => 'traveler_passport_expire_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-2">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['birth_day'], 'title' => $lang['birth_day'], 'data-view-mode' => 'years', 'class' => 'datepicker', 'name' => 'traveler_birth_date_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-1">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['nationality'], 'title' => $lang['nationality'], 'name' => 'traveler_nationality_' . $id], 'attr_short' => $input_extra, 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-1">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['flight_number'], 'title' => $lang['flight_number'], 'name' => 'traveler_flight_number_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '<div class="md-1">' . $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['border'], 'title' => $lang['border'], 'name' => 'traveler_border_' . $id], 'attr_short' => $input_extra]) . '</div>';
		$data .= '</div>';
		return $data;
	}
	///////////////////////////////////////////
	function gen_room_listing_table_head()
	{
		global $lang;
		$data = '<div class="row table-head grey align-center">
	    <div class="md-2">' . $lang['name'] . '</div>
		<div class="md-1">' . $lang['passport_no'] . '</div>
		<div class="md-2">' . $lang['issue_date'] . '</div>
	    <div class="md-2">' . $lang['expire_date'] . '</div>
	    <div class="md-2">' . $lang['birth_day'] . '</div>
	    <div class="md-1">' . $lang['nationality'] . '</div>
	    <div class="md-1">' . $lang['flight_number'] . '</div>
	    <div class="md-1">' . $lang['border'] . '</div>
	    </div>';
		return $data;
	}
	///////////////////////////////////////////
	function gen_room_listing_fields($single, $double, $twin, $triple, $quad, $total_travelers, $nights, $hotel_grade = 0, $input_extra = NULL, $guranteed_departure = NULL)
	{
		global $G, $tours_lang;
		$c = 0;
		$data = NULL;
		if ($nights == 0 or $hotel_grade == 0) {
			
				$data .= $this->gen_room_listing_table_head();
				for ($i = 1; $i <= $total_travelers; $i++) {
					$c = $c + 1;
						$data .= $this->gen_traveler_fields('s_'.$i . $guranteed_departure.'_1',$c, $input_extra);
					}

				$data = '<div class="bordered">' . $data . '</div>';
			}
		if ($nights > 0 && $hotel_grade > 0) {
				$data .= $this->gen_room_listing_table_head();
				for ($i = 1; $i <= $single; $i++) {
						$c = $c + 1;
						$data .= '<div class="bordered d-gap-b">
	   	   	<div class="align-center pad grey bordered-b"><b>' . $tours_lang['room'] . ' #' . $c . ' ' . $tours_lang['single'] . '</b></div>
	   	   	';
						$data .= $this->gen_traveler_fields('s_' . $i . $guranteed_departure . '_1', $c, $input_extra);
						$data .= '</div>';
					}

				//////////////////////////////////////
				for ($i = 1; $i <= $double; $i++) {
						$c = $c + 1;
						$data .= '<div class="bordered d-gap-b">
	   	   	<div class="align-center pad grey bordered-b"><b>' . $tours_lang['room'] . ' #' . $c . ' ' . $tours_lang['double'] . '</b></div>
	   	   	';
						$data .= $this->gen_traveler_fields('d_' . $i . $guranteed_departure . '_1', $c, $input_extra);
						$data .= $this->gen_traveler_fields('d_' . $i . $guranteed_departure . '_2', $c, $input_extra);
						$data .= '</div>';
					}

				//////////////////////////////////////
				for ($i = 1; $i <= $twin; $i++) {
						$c = $c + 1;
						$data .= '<div class="bordered d-gap-b">
	   	   	<div class="align-center pad grey bordered-b"><b>' . $tours_lang['room'] . ' #' . $c . ' ' . $tours_lang['twin'] . '</b></div>
	   	   	';

						$data .= $this->gen_traveler_fields('t_' . $i . $guranteed_departure . '_1', $c, $input_extra);
						$data .= $this->gen_traveler_fields('t_' . $i . $guranteed_departure . '_2', $c, $input_extra);
						$data .= '</div>';
					}
				//////////////////////////////////////
				for ($i = 1; $i <= $triple; $i++) {
						$c = $c + 1;
						$data .= '<div class="bordered d-gap-b">
	   	   	<div class="align-center pad grey bordered-b"><b>' . $tours_lang['room'] . ' #' . $c . ' ' . $tours_lang['triple'] . '</b></div>
	   	   	';

						$data .= $this->gen_traveler_fields('tr_' . $i . $guranteed_departure . '_1', $c, $input_extra);
						$data .= $this->gen_traveler_fields('tr_' . $i . $guranteed_departure . '_2', $c, $input_extra);
						$data .= $this->gen_traveler_fields('tr_' . $i . $guranteed_departure . '_3', $c, $input_extra);
						$data .= '</div>';
					}
				//////////////////////////////////////
				for ($i = 1; $i <= $quad; $i++) {
						$c = $c + 1;
						$data .= '<div class="bordered d-gap-b">
	   	   	<div class="align-center pad grey bordered-b"><b>' . $tours_lang['room'] . ' #' . $c . ' ' . $tours_lang['quad'] . '</b></div>
	   	   	';

						$data .= $this->gen_traveler_fields('q_' . $i . $guranteed_departure . '_1', $c, $input_extra);
						$data .= $this->gen_traveler_fields('q_' . $i . $guranteed_departure . '_2', $c, $input_extra);
						$data .= $this->gen_traveler_fields('q_' . $i . $guranteed_departure . '_3', $c, $input_extra);
						$data .= $this->gen_traveler_fields('q_' . $i . $guranteed_departure . '_4', $c, $input_extra);
						$data .= '</div>';
					}
			}

		return $data;
	}
	/////////////////////////////////////////
	function get_guaranteed_departure_price($arr)
	{
		$price['adult'] = $arr['adult_price'];
		$price['child'] = $arr['child_price'];
		if ($this->date_in_range($arr['early_bird_from_date'], $arr['early_bird_to_date'])) {
				$price['adult'] = $arr['early_bird_price'];
				$price['child'] = $arr['child_early_bird_price'];
			}
		if ($this->date_in_range($arr['last_minute_from_date'], $arr['last_minute_to_date'])) {
				$price['adult'] = $arr['last_minute_price'];
				$price['child'] = $arr['child_last_minute_price'];
			}
		return $price;
	}
	/////////////////////////////////////////
}
/////////////////////////////////////
$tourslib = new Tours;

include_once $GOGIES['path'] . '/core/countries_cache.php';
require $GOGIES['path'] . '/modules/tours/lang/' . $GOGIES['lang'] . '/lang_data.php';
$pricing_bases_vals = [0 => $tours_lang['no_hotel_accommodations'], 1 => '1 ' . $tours_lang['star'], 2 => '2 ' . $tours_lang['star'], 3 => '3 ' . $tours_lang['star'], 4 => '4 ' . $tours_lang['star'], 5 => '5 ' . $tours_lang['star']];
