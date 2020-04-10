<?php

function covid19ImpactEstimator($data)
{
  
  //Number of days 
  $days = get_num_days($data["periodType"],$data['timeToElapse']);
  //Hospital Beds 
  $availableBeds = ceil($data['totalHospitalBeds'] * 0.35);
  //reported cases 
  $reportedCases = $data['reportedCases'];
  //Calculating the impact
  $impact = get_impact($reportedCases,$availableBeds,$days);

  //Calculation of the severe impact
  $severeImpact = get_severe_impact($reportedCases,$availableBeds,$days);

  //calculation of severe  cases requested by time
  //combining the return data 
  $outputData = array(
        "data"=>$data,
        "impact"=>$impact,
        "severeImpact"=>$severeImpact
  );
  
  return $outputData;
}

//getting the impact
function get_impact($data,$hospitalBeds,$days)
{

  $impactCurrentlyInfected = $data['reportedCases']*10;
  
  $infectionsByRequestedTime = $impactCurrentlyInfected * pow(2,floor($days/3 ));
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;
  //calculation of dollarInFlight
  $dollarInFlight =number_format((float)($infectionsByRequestedTime *$data['region']['avgDailyIncomePopulation'] * $data['region']['avgDailyIncomeInUSD']  * $days),2,".","");
  $impact = array(
          "currentlyInfected"=>$impactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime" => $severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($hospitalBeds-$severeCasesByRequestedTime),
          "casesForICUByRequestedTime"=>(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>$dollarInFlight
  );

  return $impact;
}

//function to get the severe impact
function get_severe_impact($data,$hospitalBeds,$days)
{
  $severeImpactCurrentlyInfected = $data['reportedCases'] * 50;

  //calculation of the number of days
  $infectionsByRequestedTime = $severeImpactCurrentlyInfected * pow(2,floor($days/3 ));

  //calculation of cases requested overtime
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;

   //calculation of dollarInFlight
   $dollarInFlight =number_format((float)($infectionsByRequestedTime *$data['region']['avgDailyIncomePopulation'] * $data['region']['avgDailyIncomeInUSD']  * $days),2,".","");


  $severeImpact = array(
          "currentlyInfected"=>$severeImpactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime"=>$severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($hospitalBeds-$severeCasesByRequestedTime),
          "casesForICUByRequestedTime"=>(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>$dollarInFlight
  );
  return $severeImpact;
}
//function to get the number of days basing on weeks, months or days
function get_num_days($type,$number)
{
  $days = 0;
  $number = intval($number);
  switch ($type) {
    case 'days':
      # code...
      $days = $number;
      break;
    case 'months':
      # code...
      $days = $number * 30;
      break;
    case 'weeks':
      $days = $number * 7;
      break;
    default:
      # code...
      echo "Unknown Time format";
      $days = 0;
  }

  return $days;
}