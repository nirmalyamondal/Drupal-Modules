/* global Drupal */

/**
 * @file
 * Provides `Country Settings` data via GeoIP Content Module.
 */

(function ($, Drupal, drupalSettings) {

'use strict';

    Drupal.behaviors.geoip_content = {
        attach: function (context, settings) {
            var countryCode = settings.geoipCountry?settings.geoipCountry:'IN';
            //alert('countryCode='+countryCode);
            if(countryCode){
                $("div.page-wrap h2.locate-us-at").after($("div."+countryCode).clone());
                $("div."+countryCode+":last").remove();           
            }
        } // attach
    };
 })(jQuery, Drupal, drupalSettings);


/*
// WORKING
 
if($("section.upcoming-webinars ul.webinar-info li:first").text().trim().length > 1 ){
	var wInarDateText	= $("section.upcoming-webinars ul.webinar-info li:first").text().trim();
	var wInarDate 	= wInarDateText.split(" ");	//alert(wInarDate[1].replace(/^,|,$/g,''));

	var wInarTimeText	= $("section.upcoming-webinars ul.webinar-info li:last").text().trim();	
	var hours = Number(wInarTimeText.match(/^(\d+)/)[1]);
	var minutes = Number(wInarTimeText.match(/:(\d+)/)[1]);
	var AMPM = wInarTimeText.match(/\s(.*)$/)[1];
	if(AMPM == "PM" && hours<12) hours = hours+12;
	if(AMPM == "AM" && hours==12) hours = hours-12;
	var sHours = hours.toString();
	var sMinutes = minutes.toString();
	if(hours<10) sHours = "0" + sHours;
	if(minutes<10) sMinutes = "0" + sMinutes;

	var monthNumber = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"].indexOf(wInarDate[0].toLowerCase());
	//var monthNumber = monthNumber + 1;
	var wInarIST	= new Date(wInarDate[2],monthNumber,wInarDate[1].replace(/^,|,$/g,''),sHours,sMinutes).getTime();
	// Perfect timestamp has been calculated "wInarIST"
	//alert ('ist='+wInarIST);
	//alert(new Date(wInarIST).toUTCString());
	//Considering TimeZone = IST = +5Hrs30mins = +330Mins to milliseconds
	var utcGmtTStamp	= wInarIST - 330*60*1000;
	var offset 			= new Date().getTimezoneOffset()*60*1000;
	var localTimeStamp	= utcGmtTStamp - offset;
	//Local timestamp calculation is perfect
	//alert('ist='+wInarIST+'utcGmtTStamp='+utcGmtTStamp+'offset='+offset+'localTimeStamp='+localTimeStamp);
	var localDate	= new Date(localTimeStamp);
	var yearL    = localDate.getFullYear();
	var monthL   = localDate.getMonth();
	var dayL     = localDate.getDate();
	var hourL    = localDate.getHours();
	var minuteL  = localDate.getMinutes();
	//var secondsL = localDate.getSeconds();  
	var amPmLocal	= hourL >= 12 ? 'PM' : 'AM';
	hourL = hourL % 12;
	hourL = hourL ? hourL : 12; // the hour '0' should be '12'
	minuteL = minuteL < 10 ? '0'+minuteL : minuteL;

	var monthArray = new Array();
	monthArray[0] = "January";
	monthArray[1] = "February";
	monthArray[2] = "March";
	monthArray[3] = "April";
	monthArray[4] = "May";
	monthArray[5] = "June";
	monthArray[6] = "July";
	monthArray[7] = "August";
	monthArray[8] = "September";
	monthArray[9] = "October";
	monthArray[10] = "November";
	monthArray[11] = "December";
	//alert('monthNumber='+monthNumber+'year='+year+'month='+month+'day='+day+'hour='+hour+'minute='+minute);

	$("section.upcoming-webinars ul.webinar-info li:first").html('<i class="fa fa-calendar"></i>'+monthArray[monthL]+' '+dayL+', '+yearL);
	$("section.upcoming-webinars ul.webinar-info li:last").html('<i class="fa fa-clock-o"></i>'+hourL+':'+minuteL+' '+amPmLocal);

}
*/

/*
var wInarDateText	= $("section.upcoming-webinars ul.webinar-info li:first").text().trim();
//Source: 'November 19, 2016 01:35 AM'
//var newDate="Month/Date/YYYY";
var wInarDate 	= wInarDateText.split(" ");	//alert(wInarDate[1].replace(/^,|,$/g,''));

var wInarTimeText	= $("section.upcoming-webinars ul.webinar-info li:last").text().trim();	
var hours = Number(wInarTimeText.match(/^(\d+)/)[1]);
var minutes = Number(wInarTimeText.match(/:(\d+)/)[1]);
var AMPM = wInarTimeText.match(/\s(.*)$/)[1];
if(AMPM == "PM" && hours<12) hours = hours+12;
if(AMPM == "AM" && hours==12) hours = hours-12;
var sHours = hours.toString();
var sMinutes = minutes.toString();
if(hours<10) sHours = "0" + sHours;
if(minutes<10) sMinutes = "0" + sMinutes; 

//var monthNumber = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"].indexOf(wInarDate[0].toLowerCase()) + 1;
var monthNumber = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"].indexOf(wInarDate[0].toLowerCase());
//alert(wInarDate[2]+monthNumber+wInarDate[1].replace(/^,|,$/g,'')+sHours+sMinutes);
var wInarIST	= new Date(wInarDate[2],monthNumber,wInarDate[1].replace(/^,|,$/g,''),sHours,sMinutes).getTime();
//alert(new Date(wInarIST).toUTCString());
//Considering TimeZone = IST = +5Hrs30mins = +330Mins to milliseconds
var utcGmtTStamp	= wInarIST - 330*60*1000;
var offset 			= new Date().getTimezoneOffset()*60*1000;
var localTimeStamp	= utcGmtTStamp - offset;

alert('ist='+wInarIST+'utcGmtTStamp='+utcGmtTStamp+'offset='+offset+'localTimeStamp='+localTimeStamp);
alert(new Date(localTimeStamp));
//var wInarISTx	= new Date(2015,12,8,0,0,0,0);
//alert(new Date().getTime());
//alert(new Date(new Date().getTime()).toUTCString());
//1 day = 86400-000 milli-seconds
*/
