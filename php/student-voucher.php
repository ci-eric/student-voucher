<?php

							/************************************************************

								Student Assitance Attendance Voucher Application

									created by: Daniel Larkins
												Eric Nelson
												Evan Taylor

									created on:	October 7, 2013 

									version ##:	0.1.5

							 ************************************************************/

	date_default_timezone_set('America/Los_Angeles');

	$thisMonth = date("m");
	$thisYear = date("Y");

	$selectMonth = '';
	$selectYear = '';



	/************************************************************

		Calendar class
		
		contains functions that helps in the creation of the 
			Calendar for the Student Voucher Application


		test_input
			$data
			return $data
		
		getFirstDayofCalendar
			$month, $year
			return $date

		getWeeksofCalendar
			$currDay, $daysInCurrMonth, $currMonth, $month, $year
			return $weeks

		createUser

		createTable
			$month, $year

		createMYselector
			$thisYear, $selectYear, $month

	 ************************************************************/
	class Calendar {


		/************************************************************

		 	W3schools test_input function
		 	www.w3schools.com

		 ************************************************************/
		public static function test_input($data)
		{
			$data = trim($data);
			//$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}


		/************************************************************

		 	getFirstDayofCalendar
		 		$month
		 		$year

		 	gets the First day of the calendar by taking the month
		 		and year given and determining if the first day of
		 		the month starts on a sunday.  If it does, it just
		 		returns 1, the first day that the week starts on.
		 		Otherwise, it determines which day of the previous
		 		month is the last sunday of that month and returns
		 		that day

		 ************************************************************/
		public static function getFirstDayofCalendar($month, $year)
		{
			$monthStart = date("w", mktime(0, 0, 0, $month, 1, $year));
			$prevWeek = self::test_input($_POST['prevWeek']);

			if ($monthStart == 0)
			{
				if ($prevWeek == 'true')
					return date("t", mktime(0, 0, 0, $month, 1, $year)) - 7;
				else
					return 1;
			}

			if ($month > 01)
				$month--;
			else
			{
				$month = 12;
				$year--;
			}

			if ($prevWeek == 'true')
				return date("t", mktime(0, 0, 0, $month, 1, $year)) - ($monthStart - 1) - 7;
			else
				return date("t", mktime(0, 0, 0, $month, 1, $year)) - ($monthStart - 1);
		}


		/************************************************************

		 	getWeeksofCalendar
				$currDay
				$daysInCurrMonth
				$currMonth
				$month
				$year

			determines the amount of weeks needed to be created in the
				calendar

			grabs the starting day of the calendar (already handles
				previous month) and goes by week by week until it
				hits the end of the month

			if nextWeek is requested, adds one extra week before
				returning it

		 ************************************************************/

		public static function getWeeksofCalendar($currDay, $daysInCurrMonth, $currMonth, $month, $year)
		{
			$weeks = 0;

			while ($currMonth < $month + 1)
			{
				$weeks++;
				$currDay += 7;

				if ($currDay > $daysInCurrMonth)
				{
					$currDay -= $daysInCurrMonth;
					$currMonth++;
					$daysInCurrMonth = date("t", mktime(0, 0, 0, $currMonth, 1, $year));
				}
			}

			if (self::test_input($_POST['nextWeek']) == 'true')
				$weeks++;

			return $weeks;
		}


		/************************************************************

		 	createUser

		 	creates the fields that takes in the user's information
		 	only 'name', department, and supervisor fields will be 
		 		editable
		 	is bookmark capable

		 ************************************************************/
		public static function createUser()
		{
			$name = $_GET['name'];
			$dept = $_GET['dept'];
			$supr = $_GET['supr'];

			echo "	<div class='personal'>
						<div>
							NAME (Print):
							<input type='text' name='name' value='$name' />
						</div>
						<div>
							SSN#:
							<input type='text' name='ssn' disabled='disabled' />
						</div>
						<div>
							DEPARTMENT TITLE AND #:
							<input type='text' name='dept' value='$dept' />
						</div>
						<div>
							SUPERVISOR NAME and TITLE:
							<input type='text' name='supr' value='$supr' />
						</div>
						<div>
							HOURLY RATE:
							<input type='text' name='hourly' disabled='disabled' />
						</div>
					</div>";
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
		public static function createTable($month, $year)
		{
			$j = 0;
			$totalHours = 0;
			$currDay = self::getFirstDayofCalendar($month, $year);

			if ($currDay == 1)
				$currMonth = $month;
			else
				$currMonth = $month - 1;

			$daysInCurrMonth = date("t", mktime(0, 0, 0, $currMonth, 1, $year));
			$weeks = self::getWeeksofCalendar($currDay, $daysInCurrMonth, $currMonth, $month, $year);

			echo "	<div class='calendar'>
					<table class='time-sheet'>";
			echo "	<thead>
				        <tr>
				            <th>SUNDAY</th>
				            <th>MONDAY</th>
				            <th>TUESDAY</th>
				            <th>WEDNESDAY</th>
				            <th>THURSDAY</th>
				            <th>FRIDAY</th>
				            <th>SATURDAY</th>
				            <th>WEEKLY HOURS</th>
				        </tr>
				    </thead>";
			echo "<tbody>";

			while ($j < $weeks)
			{
				$weeklyHours = 0;

				echo "<tr>";

				for ($i = 0; $i < 7; $i++)
				{
					echo "	<td class='cal-cell'>
								<div class='cal-cell-date'>
									$currDay
								</div>";

						$hours = isset($_POST['Table'])? self::test_input($_POST['Table'][$j][$i]) : 0;
						$weeklyHours += $hours;

						echo "	<input name='Table[$j][$i]' class='calendar' type='text' value='$hours'>";

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
				
				$j++;

				if ($j == $weeks)
				{
					echo "</tbody></table>";

					echo "	<div class='total'>
				            	<div class='totaltxt'>TOTAL HOURS</div>
				            	<div id='total'>$totalHours</div>
				            </div>";
				}
				else
				{
					echo "</tr>";
				}
			}

			echo "</div>";
		}


		/************************************************************

		 	rowSelection

		 	creates checkboxes that will inform the table if 
		 		additional rows need to be added to the table

		 	after page submission, createTable and firstDayofCalendar
		 		will know the number of extra rows to add

		 ************************************************************/
		public static function rowSelection()
		{
			echo "<div class='week-selection'>
					<input type='checkbox' name='prevWeek' value='true'";

			if (self::test_input($_POST['prevWeek']) == 'true')
				echo "checked='checked'";
					

			echo "	>
					Previous Week
					
					<input type='checkbox' name='nextWeek' value='true'";

			if (self::test_input($_POST['nextWeek']) == 'true')
				echo "checked='checked'";

			echo "	>
					Next Week
				  </div>";
		}
		

		/************************************************************

		 	createMYselector
		 		$thisYear
		 		$selectYear
		 		$month

		 	displays the Month and Year selectors
		 	Month selector creates drop down menu for each month of
		 		the year with default at $month
		 	Year selector creates drop down menu for current year
		 		plus previous and next year, mostly unecessary

		 	also adds the type of student worker the user is

		 ************************************************************/
		public static function createMYselector($thisYear, $selectYear, $month)
		{
			echo "<div class='instruction'>Enter the Total hours worked for each day</div>";

			echo "<div class='month-year'>
					MONTH/YEAR: 
					";

			echo "<select class='monthSelect' name='MonthSelect'>";

			for ($i=1; $i<=12; $i++)
			{
				echo "	<option value='$i' ".($month == $i ? "selected='selected'" : "").">".date("F", mktime(0, 0, 0, $i, 1, $thisYear))."</option>";
			}

			echo "		</select>
						<select class='yearSelect' name='YearSelect'>
							<option value='"			.($thisYear-1)."' ".($thisYear-1 == $selectYear ? "selected='selected'" : "").">".($thisYear-1)."</option>
							<option value='$thisYear' "	.($thisYear == $selectYear ? "selected='selected'" : "").">$thisYear</option>
							<option value='"			.($thisYear+1)."' ".($thisYear+1 == $selectYear ? "selected='selected'" : "").">".($thisYear+1)."</option>
						</select>";

			switch(self::test_input($_POST['sa']))
			{
				case 'fsa':
					echo 	"<input id='fsa' type='radio' class='sa' name='sa' value='fsa' checked='checked'><label for='fsa'>Federal Work Study Student Assistant</label>";
					echo 	"<input id='sa' type='radio' class='sa' name='sa' value='sa'><label for='sa'>Student Assistant</label>";
					echo 	"<input id='bsa' type='radio' class='sa' name='sa' value='bsa'><label for='bsa'>Bridge Student Assistant</label>";
					break;

				case 'sa':
					echo 	"<input id='fsa' type='radio' class='sa' name='sa' value='fsa'><label for='fsa'>Federal Work Study Student Assistant</label>";
					echo 	"<input id='sa' type='radio' class='sa' name='sa' value='sa' checked='checked'><label for='sa'>Student Assistant</label>";
					echo 	"<input id='bsa' type='radio' class='sa' name='sa' value='bsa'><label for='bsa'>Bridge Student Assistant</label>";
					break;

				case 'bsa':
					echo 	"<input id='fsa' type='radio' class='sa' name='sa' value='fsa'><label for='fsa'>Federal Work Study Student Assistant</label>";
					echo 	"<input id='sa' type='radio' class='sa' name='sa' value='sa'><label for='sa'>Student Assistant</label>";
					echo 	"<input id='bsa' type='radio' class='sa' name='sa' value='bsa' checked='checked'><label for='bsa'>Bridge Student Assistant</label>";
					break;

				default:
					echo 	"<input id='fsa' type='radio' class='sa' name='sa' value='fsa'><label for='fsa'>Federal Work Study Student Assistant</label>";
					echo 	"<input id='sa' type='radio' class='sa' name='sa' value='sa'><label for='sa'>Student Assistant</label>";
					echo 	"<input id='bsa' type='radio' class='sa' name='sa' value='bsa'><label for='bsa'>Bridge Student Assistant</label>";
					break;
			}

			echo"</div>";
		}
	}

	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$selectMonth = 	Calendar::test_input($_POST['MonthSelect']);
		$selectYear = 	Calendar::test_input($_POST['YearSelect']);
	}
	else
	{
		$selectMonth = $thisMonth;
		$selectYear = $thisYear;
	}

	echo "<form method='post'>";

	Calendar::createUser();
	Calendar::rowSelection();

	Calendar::createMYselector($thisYear, $selectYear, $selectMonth);



	Calendar::createTable($selectMonth, $selectYear);

	echo "<input class='btn' type='submit'></form>";
?>