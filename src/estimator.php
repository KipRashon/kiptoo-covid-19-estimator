<?php

function covid19ImpactEstimator($data)
{
  //Number of days 
  $days = get_num_days($data["periodType"],$data['timeToElapse']);
  //Hospital Beds 
  $hospitalBeds = $data['totalHospitalBeds'];
  //reported cases 
  $reportedCases = $data['reportedCases'];
  //Calculating the impact
  $impact = get_impact($reportedCases,$hospitalBeds,$days);

  //Calculation of the severe impact
  $severeImpact = get_severe_impact($reportedCases,$hospitalBeds,$days);

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
function get_impact($reportedCases,$hospitalBeds,$days)
{
  $impactCurrentlyInfected = $reportedCases*10;
  $infectionsByRequestedTime = $impactCurrentlyInfected * pow(2,$days);
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;
  $impact = array(
          "currentlyInfected"=>$impactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime" => $severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($severeCasesByRequestedTime-$hospitalBeds),
          "casesForICUByRequestedTime"=>(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>number_format((float)($infectionsByRequestedTime *0.65 * 1.5 * 30),2,".","")  //converts to two decimal places  
  );

  return $impact;
}

//function to get the severe impact
function get_severe_impact($reportedCases,$hospitalBeds,$days)
{
  $severeImpactCurrentlyInfected = $reportedCases * 50;
  $infectionsByRequestedTime = $severeImpactCurrentlyInfected * pow(2,$days);
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;
  $severeImpact = array(
          "currentlyInfected"=>$severeImpactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime"=>$severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($severeCasesByRequestedTime-$hospitalBeds),
          "casesForICUByRequestedTime"=>(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>number_format((float)($infectionsByRequestedTime *0.65 * 1.5 * 30),2,".","")  //converts to two decimal places      
  );
  return $severeImpact;
}
//function to get the number of days basing on weeks, months or days
function get_num_days($type,$number)
{
  $days = 0;
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
    default:
      # code...
      echo "Unknown Time format";
      $days = 0;
      break;
      //change the days to periods
      $days = (int)$days/3;
  return $days;
  }
}