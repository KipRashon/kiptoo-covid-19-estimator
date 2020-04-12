<?php

function covid19ImpactEstimator($data)
{

  //Number of days
  $days = get_num_days($data->periodType,$data->timeToElapse);
  //Hospital Beds
  $availableBeds = ceil($data->totalHospitalBeds * 0.35);
  //reported cases
  $reportedCases = $data->reportedCases;
  //Calculating the impact
  $impact = get_impact($reportedCases,$availableBeds,$days,$data->region);

  //Calculation of the severe impact
  $severeImpact = get_severe_impact($reportedCases,$availableBeds,$days,$data->region);

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
function get_impact($reportedCases,$hospitalBeds,$days,$region)
{
  $impactCurrentlyInfected = $reportedCases*10;
  $infectionsByRequestedTime = $impactCurrentlyInfected * pow(2,floor($days/3));
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;
  $dollarFlight = floor($infectionsByRequestedTime *$region->avgDailyIncomePopulation * $region->avgDailyIncomeInUSD / $days);
  $impact = array(
          "currentlyInfected"=>$impactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime" => $severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($hospitalBeds-$severeCasesByRequestedTime),
          "casesForICUByRequestedTime"=>floor(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>floor(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>  $dollarFlight
  );

  return $impact;
}

//function to get the severe impact
function get_severe_impact($reportedCases,$hospitalBeds,$days,$region)
{
  $severeImpactCurrentlyInfected = $reportedCases * 50;
  $infectionsByRequestedTime = $severeImpactCurrentlyInfected * pow(2,floor($days/3));
  $severeCasesByRequestedTime = 0.15 * $infectionsByRequestedTime;
  $dollarFlight = floor($infectionsByRequestedTime * $region->avgDailyIncomePopulation * $region->avgDailyIncomeInUSD / $days);

  $severeImpact = array(
          "currentlyInfected"=>$severeImpactCurrentlyInfected,
          "infectionsByRequestedTime"=>$infectionsByRequestedTime,
          "severeCasesByRequestedTime"=>$severeCasesByRequestedTime,
          "hospitalBedsByRequestedTime"=>($hospitalBeds-$severeCasesByRequestedTime),
          "casesForICUByRequestedTime"=>floor(0.05 * $infectionsByRequestedTime),
          "casesForVentilatorsByRequestedTime"=>floor(0.02 * $infectionsByRequestedTime),
          "dollarsInFlight"=>$dollarFlight
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
