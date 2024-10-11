<?php

class Youtube2Mp3
{

	private $tokenApi = "https://yt1s.com/api/ajaxSearch/index";
	private $downloadLinkApi = "https://yt1s.com/api/ajaxConvert/convert";
	private $checkStatusApi = "https://yt1s.com/api/ajaxConvert/checkTask";

	function generateToken($URL,$type)
	{

		$post = [
			"q"=>$URL,
			"vt"=>"mp3"
		];

		$handler = curl_init($this->tokenApi);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $post);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($handler);

		curl_close($handler);

		return $response;
	}
	function checkTask($vid, $b_id)
	{

		$post = [
			"vid"=>$vid,
			"b_id"=>$b_id
		];

		$handler = curl_init($this->checkStatusApi);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $post);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($handler);

		curl_close($handler);

		return $response;

	}
	function getDownloadLink($vid, $k)
	{

		$post = [
			"vid"=>$vid,
			"k"=>$k
		];

		$handler = curl_init($this->downloadLinkApi);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $post);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($handler);

		curl_close($handler);

		return $response;

	}

	function getToken($URL,$type)
	{

		$result = [];

		try
		{
			$token = $this->generateToken($URL,$type);
			$tokenDecoded = json_decode($token,true);
			$vid = $tokenDecoded["vid"] ?? false;

			if($tokenDecoded["status"] == "ok")
			{
				$result["error"] = null;
				$result["data"] = $tokenDecoded;
				return $result;
			}
			else
			{
				$result["error"] = "Failed to generate token";
				return $result;
			}
		
		}
		catch(Exception $e)
		{
			$result["error"] = "Error has occured";
			return $result;
		}
		
	}

	function download($k,$vid)
	{
		

			if($vid == false || $k == false)
			{
				$result["error"] = "Failed to generate token";
				return $result;
			}
			

			$linkResponse = $this->getDownloadLink($vid, $k);
			$linkResponseDecoded = json_decode($linkResponse,true);

			$c_status = $linkResponseDecoded["c_status"] ?? "CONVERTING";
			$b_id = $linkResponseDecoded["b_id"] ?? false;

			if($c_status == "CONVERTED")
			{
				$result["error"] = null;
				$result["data"] = $linkResponseDecoded;
				return $result;
			}

			if($b_id == false && $c_status != "CONVERTED")
			{
				$result["error"] = "Failed to generate download link";
				return $result;
			}


			$convertStats = "CONVERTING";
			$data = [];
			do
			{
				$taskResponse = $this->checkTask($vid, $b_id);
				$taskResponseDecoded = json_decode($taskResponse, true);
				$convertStats = $taskResponseDecoded["c_status "];
				$data = $taskResponseDecoded;
			}
			while($convertStats == "CONVERTING");

			
			if($convertStats == "CONVERTED")
			{
				$result["error"] = null;
				$result["data"] = $data;
				return $result;
			}
			else
			{
				$result["error"] = "Failed to generate mp3 file";
				return $result;
			}
	}

}
