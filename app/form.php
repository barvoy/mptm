<?php
// @todo: modefiles
// @todo: regex in fields to check for correctness?
declare(strict_types=1);
error_reporting(-1);

include_once('lib.php');
fail_on_error();
// @todo: uncomment after development
//if (TRUE) {
//	state_init();
//	state_trans_from_to("init", "form");
//}
// @todo: find out standard field names so that the auto-fill wokrs ok
?>

<html>
<head>
		<link rel="stylesheet" href="tm.css">
</head>

<body>

	<h1>Join Menlo Park Toastmasters!</h1>
	<!--
	<p>
		Join our club in 2 easy steps:
		<ol>
			<li>
				Fill the form and pay membership fees (below)
			</li>
			<li>
				Join
				<a target="_blank" href="https://groups.google.com/forum/#!forum/mptm/join">our club's mailing list</a>.
				This is how we send speech details and our schedule.
			</li>
		</ol>
	</p>
	-->

	<span class="req">*</span> fields are required
	<br />

	<form action="save.php" method="post">

	<fieldset>
		<legend>Personal information</legend>

		<div>
			<label for="firstname">First name:</label>
			<span class="req">*</span>
			<br />
			<input type="text" id="firstname" name="firstname" placeholder="..." required>
		</div>
		<br />

		<div>
			<label for="middlename">Middle name:</label>
			<br />
			<input type="text" id="middlename" name="middlename" placeholder="...">
		</div>
		<br />

		<div>
			<label for="lastname">Last name/Surname:</label>
			<span class="req">*</span>
			<br />
			<input type="text" id="lastname" name="lastname" placeholder="..." required>
		</div>
		<br />


		<div>
			<label for="gender">Gender:</label>
			<br />
			<input type="radio" name="gender" value="male" />Male
			<input type="radio" name="gender" value="female" />Female
			<input type="radio" name="gender" value="other" checked />Other
		</div>
		<br />


		<div class="lineitem">
			<label for="address1">Address 1:</label>
			<span class="req">*</span>
			<br />
			<input required type="text" id="address1" name="address1" placeholder="Address 1..." width="100%" />
		</div>
		<br />

		<div class="lineitem">
			<label for="address2">Address 2:</label>
			<br />
			<input type="text" id="address2" name="address2" placeholder="Address 2..." width="100%" />
			<br />
		</div>
		<br />

		<div class="lineitem">
			<label for="zipcode">ZIP code:</label>
			<span class="req">*</span>
			<br />
			<input required type="text" id="zipcode" name="zipcode" placeholder="xxxxx" size=5>
			<br />
		</div>
		<br />

		<div class="lineitem">
			<label for="cityname">City:</label>
			<span class="req">*</span>
			<br />
			<input required type="text" id="city" name="city" placeholder="Your city name..." size=20 />
			<br />
		</div>
		<br />

		<div class="lineitem">
			<label for="state">State:</label>
			<span class="req">*</span>
			<br />
			<input required type="text" id="state" name="state" placeholder="Your state..." size=20 />
			<br />
		</div>
		<br />

		<div class="lineitem">
			<label for="email">E-mail:</label>
			<span class="req">*</span>
			<br />
			<input required type="text" id="email" name="email" placeholder="Your e-mail..." size=20 />
			<br />
		</div>
	</fieldset>




	<fieldset width="80%">
		<legend>Membership options:</legend>

		<p>
			<b>Are you a new member?</b>
			<br />
			<input type="radio" class="mptm_new_member_dues" name="mptm_new_member" value="1" checked="true" />yes ($20, initiation fee)<br/>
			<input type="radio" class="mptm_new_member_dues" name="mptm_new_member" value="0" />no &nbsp;($0, renewal/reinstating)<br/>
		</p>

		<p>
			<b>Month you'd like to start your membership</b><br />
			<small>
			(pro-rated $10/month: $7.50/month to Toastmasters International 503c non-profit and $2.50/month for running our club):
			</small>
			<br />
			<br />
			<input type="radio" class="mptm_months" name="mptm_start_month" value="jan" />January<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="feb" />February<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="mar" />March<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="apr" />April<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="may" />May<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="jun" />June<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="jul" />July<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="aug" />August<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="sep" />September<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="oct" />October<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="nov" />November<br/>
			<input type="radio" class="mptm_months" name="mptm_start_month" value="dec" />December<br/>
		</p>
	</fieldset>


	<fieldset>
		<legend>
			<span class="req">*</span>
			Membership Type
		</legend>

		<input type="radio" name="membership_type" class="membership_type" value="new" checked>New</input><br />
		<input type="radio" name="membership_type" class="membership_type" value="reinstated">Reinstated (break in membership)</input><br />
		<input type="radio" name="membership_type" class="membership_type" value="renewing">Renewing (no break in membership)</input><br />
		<input type="radio" name="membership_type" class="membership_type" value="dual">Dual</input><br />
		<input type="radio" name="membership_type" class="membership_type" value="transfer">Transfer</input><br />
		<br />

		<span id="membership_type_transfer_data" hidden>
			<h3>Transferring: provide details here</h3>
			Previous club name:
			<span class="req">*</span>
			<input type="text" name="trans_prev_club_name" placeholder="Previous club name" />
			<br />

			Previous club number:
			<span class="req">*</span>
			<input type="text" name="trans_prev_club_num" placeholder="Previous club number" />
			<br />

			Member number:
			<span class="req">*</span>
			<input type="text" name="trans_memb_number" placeholder="Member number" />
			<br />
		</span>


	</fieldset>

	<fieldset hidden>
		<legend>Previous Club information</legend>

		<label for="club_city">Club city:</label>
		<input type="text" id="club_city" name="club_city" value="Menlo Park" size=30 />
		<br />

		<label for="club_name">Club name:</label>
		<input type="text" id="club_name" name="club_name" value="Menlo Park Toastmasters" />

		<label for="club_number">Club number:</label>
		<input type="text" id="club_number" name="club_number" value="1372" />
	</fieldset>

	<fieldset>
		<legend>
			<span class="req">*</span>
			A Toastmaster's Promise
		</legend>

		As a member of Toastmasters International and my club, I promise:
		<p>
			<li>To attend club meetings regularly</li>
			<li>To prepare all of my projects to the best of my ability, basing them on the Toastmasters education program</li>
			<li>To prepare for and fulfill meeting assignments</li>
			<li>To provide fellow members with helpful, constructive evaluations</li>
			<li>To help the club maintain the positive, friendly environment necessary for all members to learn and grow</li>
			<li>To serve my club as an officer when called upon to do so</li>
			<li>To treat my fellow club members and our guests with respect and courtesy</li>
			<li>To bring guests to club meetings so they can see the benefits Toastmasters membership offers</li>
			<li>To adhere to the guidelines and rules for all Toastmasters education and recognition programs</li>
			<li>To act within Toastmasters’ core values of integrity, respect, service and excellence during the conduct of all Toastmasters activities</li>
		</p>
			<input type="checkbox" name="tm_agree_promise" required>
			<span class="req">*</span>
			<span class="req_mark">I agree with Toastmaster's Promise</span>
		<br />
	</fieldset>
	<br />

	<fieldset>
		<legend>Agreements</legend>

		<span class="req_mark">
			<input type="checkbox" name="tm_agree_release" required>
			<span class="req">*</span>
			I agree with Member's Agreement and Release rules</input> (<a target="_blank" href="tm_members_agreement.html">click here to read</a>)<br />
		</span>
		<br />

		<span class="req_mark">
			<input type="checkbox" name="tm_agree_verif" required>
			<span class="req">*</span>
			I agree with the Verification of the Applicant</input> (<a target="popup" href="tm_verif.html">click here to read</a>)<br />
		</span>
	</fieldset>
	<br />

	<fieldset>
		<legend>Summary</legend>

		<ul>
		<li><span id="pay_expl_link"></span>
		</ul>
		</p>

		<p>
		<h3 id="mptm_total_pay">Total: </h3>
		</p>

		<p>
		<a id="pay_link" href=""></a>
		<br/>
		<br/>
		<i>NOTE: You'll be redirected to the PayPal website. Credit/debit cards and PayPal are accepted.</i>
		</p>
	</fieldset>

	<input class="submit" type="submit" />

	</form>

<script>

function loadJSON(callback) {
	var xobj = new XMLHttpRequest();
	xobj.overrideMimeType("application/json");
	xobj.open('GET', 'club.php?alias=mptm', true);
	xobj.onreadystatechange = function() {
		if (xobj.readyState == 4 && xobj.status == "200") {
			console.log("everything ok!");
			callback(xobj.responseText);
		}
	}
	xobj.send(null);
}

club_info = null;

loadJSON(function(response) {
	console.log(response);
	club_info = JSON.parse(response);
	mptm_calc_dues(true);	// call it once to update a "checked" mark near the current month
});

</script>
<script src="tm.js"></script>



<hr />
<small>
	<center>
		Copyright &copy; 2018 W. Adam Koszek and <a href="http://www.barvoy.com">Barvoy LLC</a> (contact: <a href="mailto:wkoszek@koszek.com">wojciech@koszek.com</a>)
	</center>
</small>

</body>
</html>
