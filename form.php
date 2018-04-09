<?php
include_once('lib.php');
	state_init();
	state_trans_from_to("index", "form");
?>

<style>
body { font-family: sans-serif; }
input.submit { background-color: lightyellow ; width: 10%; font-size: 100%; }
span.req { color: red; }
input { background-color: lightyellow; }
legend { yellow; border: dotted 1px lightgray ;}
.req_mark { background-color: lightyellow; }
</style>

<h4>We're happy to see you joining Menlo Park Toastmasters Club!<h4>
<h1>Application & Payment Information</h1>


<style>
.left { float: left; margin-right: 2%; }
.line { margin-bottom: 1%; }
.none { float: none; }
</style>

<span class="req">*</span> fields are required
<br />

<form action="save.php" method="post">
  <fieldset>
    <legend>Personal information</legend>

    <div class="form">
      <div class="line">
        <div class="left">
          <label for="firstname">First name:</label>
          <span class="req">*</span>
          <br />
          <input type="text" id="firstname" name="firstname" placeholder="..." required>
        </div>
        <div class="left">
          <label for="middlename">Middle name:</label>
          <br />
          <input type="text" id="middlename" name="middlename" placeholder="...">
        </div>
        <div class="none">
          <label for="lastname">Last name/Surname:</label>
          <span class="req">*</span>
          <br />
          <input type="text" id="lastname" name="lastname" placeholder="..." required>
        </div>
      </div>

      <div class="line">
        <div class="none">
          <label for="gender">Gender:</label>
          <br />
          <input type="radio" name="gender" value="male" />Male
          <input type="radio" name="gender" value="female" />Female
          <input type="radio" name="gender" value="other" checked />Other
        </div>
      </div>

      <div class="line">

        <div class="lineitem" style="width: 100%">
          <label for="address1">Address 1:</label>
          <span class="req">*</span>
          <br />
          <input required type="text" id="address1" name="address1" placeholder="Address 1..." width="100%" />
        </div>

	<br />
        <div class="lineitem" style="width: 100%">
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
          <input required type="text" id="city" name="city" placeholder="Your city name..." size=100>
          <br />
        </div>

	<br />
        <div class="lineitem">
          <label for="state">State:</label>
          <span class="req">*</span>
          <br />
          <input required type="text" id="state" name="state" placeholder="Your state..." size=100>
          <br />
        </div>


	<br />
        <div class="lineitem">
          <label for="email">E-mail:</label>
          <span class="req">*</span>
          <br />
          <input required type="text" id="email" name="email" placeholder="Your e-mail..." size=100>
          <br />
        </div>



      </div>
    </div>
  </fieldset>
<br />

<!--
<hr />
<select>
<option>English</option>
<option>German</option>
<option>Portugese</option>
<option>French</option>
<option>Spanish</option>
<option>简体中文</option>
<option>繁體中文</option>
</select>
<hr />
-->


<fieldset>
	<legend>
		<span class="req">*</span>
		Membership Type
	</legend>

	<br />
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
<br />

<fieldset hidden>
	<legend>Club information</legend>

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

	<span class="req_mark">
		<input type="checkbox" name="tm_agree_verif" required>
		<span class="req">*</span>
		I agree with the Verification of the Applicant</input> (<a target="popup" href="tm_verif.html">click here to read</a>)<br />
	</span>
</fieldset>
<br />

<input class="submit" type="submit" />

</form>

<script>
let mem_type_trans_data_el = document.getElementById('membership_type_transfer_data');
let memb_types_el = document.getElementsByClassName('membership_type');
for (memb_type_el of memb_types_el) {
	console.log(memb_type_el);
	memb_type_el.addEventListener('change', function (evt) {
		console.log(evt);
		if (evt.srcElement.value == 'transfer') {
			//evt.srcElement.hidden = !evt.srcElement.hidden;
			mem_type_trans_data_el.hidden = !mem_type_trans_data_el.hidden;
		} else {
			mem_type_trans_data_el.hidden = true;
		}
	});
}
</script>
