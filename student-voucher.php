<?php

							/************************************************************

							 	Student Assitance Attendance Voucher Application

							 		created by: Daniel Larkins
							 					Eric Nelson
							 					Evan Taylor

							 		created on:	October 7, 2013

							 		version ##:	0.1.1

							 ************************************************************/



	$thisMonth = date("m");
	$thisYear = date("Y");

	$selectMonth = '';
	$selectYear = '';

	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$selectMonth = test_input($_POST['MonthSelect']);
		$selectYear = test_input($_POST['YearSelect']);
	}
	else
	{
		$selectMonth = $thisMonth;
		$selectYear = $thisYear;
	}

	/************************************************************

	 	getMonth

	 	#returns $thisMonth

	 ************************************************************/
	function getMonth()
	{
		return $thisMonth;
	}

	/************************************************************

	 	getYear

	 	#returns $thisYear

	 ************************************************************/
	function getYear()
	{
		return $thisMonth;
	}

	/************************************************************

	 	

	 ************************************************************/
	function getFirstDayofCalendar($month, $year)
	{
		$monthStart = date("w", mktime(0, 0, 0, $month, 1, $year));

		if ($monthStart == 0)
			return 1;

		if ($month > 01)
			$month--;
		else
		{
			$month = 12;
			$year--;
		}

		return date("t", mktime(0, 0, 0, $month, 1, $year)) - ($monthStart - 1);
	}

	/************************************************************

	 	W3schools test_input function
	 	www.w3schools.com

	 ************************************************************/

	function test_input($data)
	{
		$data = trim($data);
		//$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	/************************************************************

	 	createUser

	 	creates the fields that takes in the user's information
	 	only 'name', department, and supervisor fields will be 
	 		editable
	 	is bookmark capable

	 ************************************************************/
	function createUser()
	{
		$url = $_SERVER["REQUEST_URI"];
		$parts = explode('/', $url);
		$path = $parts[count($parts) - 1];
		$user = explode('?', $path);

		if (count($user) == 4)
		{
			$name = $user[1];
			$dept = $user[2];
			$supr = $user[3];
		}
		else
		{
			$name = '';
			$dept = '';
			$supr = '';
		}

		echo "	<input type='text' value=$name>
				<input type='text' value=$dept>
				<input type='text' value=$supr>";
	}

	/************************************************************

		creatTable
			$month
			$year

		creates a table for the entire calendar
		includes each week in the month, including days from
			previous and next month from overlaps
		each day can have the number of hours worked
		column for weekly hours counts up number of hours worked
			for that week

	 ************************************************************/
	function createTable($month, $year)
	{
		$currDay = getFirstDayofCalendar($month, $year);
		if ($currDay == 1)
			$currMonth = $month;
		else
			$currMonth = $month - 1;
		$daysInCurrMonth = date("t", mktime(0, 0, 0, $currMonth, 1, $year));

		echo "<table class='time-sheet margin-left-10'>";
		echo "	<thead>
			        <tr class='bg-lightGrey'>
			            <th>SUNDAY</th>
			            <th>MONDAY</th>
			            <th>TUESDAY</th>
			            <th>WEDNESDAY</th>
			            <th>THURSDAY</th>
			            <th>FRIDAY</th>
			            <th>SATURDAY</th>
			            <th class='week'>WEEKLY HOURS</th>
			        </tr>
			    </thead>";
		echo "<tbody>";

		$j = 0;
		$totalHours = 0;

		while ($currMonth != $month + 1)
		{
			$weeklyHours = 0;

			echo "<tr>";

			for ($i = 0; $i < 7; $i++)
			{
				echo "	<td class='hours'>
							<div class='date'>
								$currDay
							</div>";

				if($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$weeklyHours += $hours = test_input($_POST['Table'][$j][$i]);

					echo "	<input name='Table[$j][$i]' class='calendar' type='text' value='$hours'>";
				}
				else
					echo "	<input name='Table[$j][$i]' class='calendar' type='text' value='0'>";

				echo "</td>";

				if (++$currDay > $daysInCurrMonth)
				{
					$currDay = 1;
					$currMonth++;
					$daysInCurrMonth = date("t", mktime(0, 0, 0, $currMonth, 1, $year));
				}
			}

			echo "<td class='weekly bold'>$weeklyHours</td>";

			$totalHours += $weeklyHours;

			if ($currMonth == $month + 1)
			{
				echo "	<td class='total bold'>
			            	<div class='totaltxt'>TOTAL HOURS</div>
			            	<div id='total'>$totalHours</div>
			            </td>";
			}
			else
				$j++;

			echo "</tr>";
		}

		echo "</tbody></table>";
	}

	/************************************************************

	 	

	 ************************************************************/
	function createMYselector($thisYear, $selectYear, $month)
	{
		echo "<select name='MonthSelect'>";

		for ($i=1; $i<=12; $i++)
		{
			echo "	<option value='$i' ".($month == $i ? "selected='selected'" : "").">".date("M", mktime(0, 0, 0, $i, 1, $thisYear))."</option>";
		}

		echo "		</select>
					<select name='YearSelect'>
						<option value='"			.($thisYear-1)."' ".($thisYear-1 == $selectYear ? "selected='selected'" : "").">".($thisYear-1)."</option>
						<option value='$thisYear' "	.($thisYear == $selectYear ? "selected='selected'" : "").">$thisYear</option>
						<option value='"			.($thisYear+1)."' ".($thisYear+1 == $selectYear ? "selected='selected'" : "").">".($thisYear+1)."</option>
					</select>";
	}

	echo "<form method='post'>";

	createUser();
	createMYselector($thisYear, $selectYear, $selectMonth);
	createTable($selectMonth, $selectYear);

	echo "<input type='submit'></form>";
?>