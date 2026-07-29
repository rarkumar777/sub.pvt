<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                           ///
 * @link		    www.cms.gogies.net                                    ///
 * @Version         1.0                                                   /// 
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies')) {
	print 'Direct script access is not allowed';
	exit;
}

///////update guaranteed dept booking status///////////
$GOGIES['guaranteed_id_sql'] = $G['db']->q('SELECT b.id as gbooking_id ,b.guaranteed_departure_id,b.trip_status, g.max_to_operate, b2.id as booking_id,b2.invoices FROM ' . $GOGIES['dbprf'] . 'tours_departure_booking b LEFT JOIN ' . $GOGIES['dbprf'] . 'tours_guaranteed_departure g ON g.id=b.guaranteed_departure_id LEFT JOIN ' . $GOGIES['dbprf'] . 'tours_booking b2 ON b2.guaranteed_departure_id=b.guaranteed_departure_id  WHERE b.invoice_id=\'' . $GOGIES['invoice_id'] . '\' LIMIT 1 ');
if (mysqli_num_rows($GOGIES['guaranteed_id_sql']) == 1) {

		$GOGIES['booking_data'] = mysqli_fetch_array($GOGIES['guaranteed_id_sql']);
		/////////////////////////////////////////////////
		if ($GOGIES['invoice_status'] === 'c' && $GOGIES['booking_data']['trip_status'] != 'can' or $GOGIES['invoice_status'] === 'pa' && $GOGIES['booking_data']['trip_status'] != 'can') {
				$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'tours_departure_booking SET trip_status=\'con\' WHERE id=\'' . $GOGIES['booking_data']['gbooking_id'] . '\' LIMIT 1');
			} else {
				if ($GOGIES['booking_data']['trip_status'] != 'can') {
					$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'tours_departure_booking SET trip_status=\'pen\' WHERE id=\'' . $GOGIES['booking_data']['gbooking_id'] . '\' LIMIT 1');
				}
			}
		/////////////////////////////////////////////////
		$GOGIES['guaranteed_booking_sql'] = $G['db']->q('select sum(`adult`) as adult,sum(`child`) as child,sum(`single`) as single, sum(`double`) as double_room,sum(`twin`) as twin , sum(`triple`) as triple,sum(`quad`) as quad  FROM ' . $GOGIES['dbprf'] . 'tours_departure_booking  WHERE trip_status=\'con\' AND guaranteed_departure_id=\'' . $GOGIES['booking_data']['guaranteed_departure_id'] . '\' ');
		$GOGIES['booking_sum'] = mysqli_fetch_array($GOGIES['guaranteed_booking_sql']);
		$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'tours_booking SET
		 `room_single`=' . intval($GOGIES['booking_sum']['single']) . ',
		 `rooms_double`=' . intval($GOGIES['booking_sum']['double_room']) . ',
		 `rooms_twin`=' . intval($GOGIES['booking_sum']['twin']) . ',
		 `rooms_triple`=' . intval($GOGIES['booking_sum']['triple']) . ',
		 `rooms_quad`=' . intval($GOGIES['booking_sum']['quad']) . ',
	     `adult`=' . intval($GOGIES['booking_sum']['adult']) . ',
	     `child`=' . intval($GOGIES['booking_sum']['child']) . '
		  WHERE id=\'' . $GOGIES['booking_data']['booking_id'] . '\' LIMIT 1
		  ');

		$GOGIES['unpaid_travelers_sql'] = $G['db']->q('SELECT SUM(`child`) as child,SUM(`adult`) as adult FROM ' . $GOGIES['dbprf'] . 'tours_departure_booking  WHERE trip_status!=\'con\' AND guaranteed_departure_id=\'' . $GOGIES['booking_data']['guaranteed_departure_id'] . '\' ');
		$GOGIES['unpaid_travelers_data'] = mysqli_fetch_array($GOGIES['unpaid_travelers_sql']);
		$GOGIES['unpaid_travelers'] = $GOGIES['unpaid_travelers_data']['child'] + $GOGIES['unpaid_travelers_data']['adult'];
		$GOGIES['paid_travelres'] = $GOGIES['booking_sum']['child'] + $GOGIES['booking_sum']['adult'];
		$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'tours_guaranteed_departure SET booked_paid=\'' . $GOGIES['paid_travelres'] . '\' ,booked_pending=\'' . $GOGIES['unpaid_travelers'] . '\' WHERE id=\'' . $GOGIES['booking_data']['guaranteed_departure_id'] . '\'  LIMIT 1');
		$GOGIES['left_seats'] = $GOGIES['booking_data']['max_to_operate'] - ($GOGIES['booking_sum']['adult'] + $GOGIES['booking_sum']['child']);
		if ($GOGIES['left_seats'] < 1) {
				$GOGIES['booking_invoices'] = unserialize($GOGIES['booking_data']['invoices'], ['allowed_classes' => FALSE]);
				$GOGIES['booking_invoices'] = implode(',', $GOGIES['booking_invoices']);
				$G['db']->q('UPDATE ' . $GOGIES['dbprf'] . 'invoices SET status=\'ca\' WHERE id IN (' . $GOGIES['booking_invoices'] . ') AND status IN (\'p\',\'u\',\'0\') ');
			}
	}

/////////////////////////////////////////////////////// 
