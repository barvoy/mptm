// @todo: mptm_months from form.php to jan,feb,mar
//   then we can get this thing into json

function month_names_short() {
	return ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
}
function month_idx(name) {
	let names = month_names_short();

	let idx = -1;

	for (let ni = 0; ni < names.length; ni++) {
		if (name == names[ni]) {
			idx = ni;
			break;
		}
	}
	return idx;
}

function mptm_calc_dues(should_update_month) {
	const ca_sales_tax = 1.55;
	const mptm_monthly_fee = 2.50;
	const tmi_monthly_fee = 7.50;
	const new_member_fee = 20;

	const cur_date = new Date();
	const cur_month_day = cur_date.getDate();
	const month_maybe_inc = (cur_month_day > 14) ? 1 : 0;
	const toggle_month = (cur_date.getMonth() + month_maybe_inc) % 12;

	let mptm_months_el = document.getElementsByClassName('mptm_months');
	let mptm_new_member_dues = document.getElementsByClassName('mptm_new_member_dues');

	console.assert(mptm_months_el.length == 12, "internal error: we don't have 12 months");
	console.assert(toggle_month >= 0 && toggle_month <= 11, "we have a wrong month!");
	if (should_update_month) {
		mptm_months_el[toggle_month].checked = 'true';
	}

	let which_month_to_start = null;
	let how_many_months = null;
	let wi = 0;
	for (let mptm_month_el of mptm_months_el) {
		if (mptm_month_el.checked) {
			// in theory we don't need that, but I want to make
			// sure names jan,feb etc are getting nicely writted
			// with POST to the output file.
			midx = month_idx(mptm_month_el.value);
			console.assert(midx >= 0 && midx <= 11, "midx !!  0..11");
			how_many_months = (midx + 3) % 12; // cycle starts in april, which is 4th month (index=3)

			which_month_to_start = wi;
			break;
		}
		wi += 1;
	}
	let is_new_member = null;
	for (let mptm_member_dues of mptm_new_member_dues) {
		if (mptm_member_dues.checked) {
			is_new_member = parseInt(mptm_member_dues.value);
		}
	}
	console.assert(how_many_months != null, "ops1");
	console.assert(is_new_member != null, "ops2");
	console.assert(which_month_to_start != null, "ops3");

	let to_pay = (mptm_monthly_fee + tmi_monthly_fee)*how_many_months
			+ (new_member_fee*is_new_member)
			+ ca_sales_tax;



	let mptm_total_pay_el = document.getElementById('mptm_total_pay');
	mptm_total_pay_el.innerHTML = "Total: $" + to_pay;

	let pay_link_el = document.getElementById('pay_link');
	pay_link_el.innerHTML = "Click HERE to pay $" + to_pay;
	pay_link_el.href = "https://paypal.me/mptm/" + to_pay;

	let tmpDate = new Date(cur_date.getFullYear(), which_month_to_start, 1);
	let start_month_name = tmpDate.toLocaleString("en-us", { month: "long" });

	let pay_expl_link_el = document.getElementById('pay_expl_link');
	pay_expl_link_el.innerHTML = how_many_months
		+ "-month membership in Menlo Park Toastmasters, starting "
		+ start_month_name
		+ " 1 ($" + (mptm_monthly_fee + tmi_monthly_fee)
		+ "/month): $" + ((mptm_monthly_fee+tmi_monthly_fee)*how_many_months)+ "<br/>"
		+ (is_new_member ? ("<li>New member initiation fee: $" + new_member_fee) : "")
		+ "<li>CA sales tax: $" + (ca_sales_tax)
		;

	//console.log(how_many_months, is_new_member);
}

let r1_el = document.getElementsByClassName('mptm_new_member_dues');
let r2_el = document.getElementsByClassName('mptm_months');

for (r_el of r1_el) {
	r_el.addEventListener('click', function() {
		mptm_calc_dues(false);
	});
}
for (r_el of r2_el) {
	r_el.addEventListener('click', function() {
		mptm_calc_dues(false);
	});
}

mptm_calc_dues(true);	// call it once to update a "checked" mark near the current month


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

