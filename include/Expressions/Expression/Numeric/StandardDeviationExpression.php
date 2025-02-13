<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once("include/Expressions/Expression/Numeric/NumericExpression.php");
/**
 * <b>stddev(Number n, ...)</b><br>
 * Returns the population standard deviation of the <br/>
 * given values.<br>
 * ex: <i>stddev(4, 5, 6, 7, 10)</i> = 2.06
 */
class StandardDeviationExpression extends NumericExpression {
	/**
	 * Returns itself when evaluating.
	 */
	function evaluate() {
		$params = $this->getParameters();
		$values = array();
		
		// find the mean
		$sum   = 0;
		$count = sizeof($params);
		foreach ( $params as $param ) {
			$value = $param->evaluate();
			$values[] = $value;
			$sum += $value;
		}
		$mean = $sum / $count;
		
		// find the summation of deviations
		$deviation_sum = 0;
		foreach ( $values  as $value )
		{
			$deviation_sum += pow($value - $mean, 2);
		}	

		// find the std dev
		$variance = (1/$count)*$deviation_sum;
		
		return sqrt($variance);
	}
	
	/**
	 * Returns the JS Equivalent of the evaluate function.
	 */
	static function getJSEvaluate() {
		return <<<EOQ
			var params = this.getParameters();
			var values = new Array();
			
			// find the mean
			var sum   = 0;
			var count = params.length;
			for ( var i = 0; i < params.length; i++ ) {
				value = params[i].evaluate();
				values[values.length] = value;
				sum += value;
			}
			var mean = sum / count;
			
			// find the summation of deviations
			var deviation_sum = 0;
			for ( var i = 0; i < values.length; i++ )
				deviation_sum += Math.pow(values[i] - mean, 2);
	
			// find the std dev
			var variance = (1/count)*deviation_sum;
			
			return Math.sqrt(variance);
EOQ;
	}
	
	/**
	 * Returns the opreation name that this Expression should be
	 * called by.
	 */
	static function getOperationName() {
		return "stddev";
	}
	
	/**
	 * Returns the String representation of this Expression.
	 */
	function toString() {
		//pass
	}
}
?>