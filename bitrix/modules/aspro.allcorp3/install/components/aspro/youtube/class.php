<?
class CAsproYoutube extends CBitrixComponent
{
	const URL_YOUTUBE_API = 'https://www.googleapis.com/youtube/v3/';

	public $access_token = 0;
	public $channel_id = 0;
	public $playlist_id = 0;
	public $count_post = 5;
	public $error = "";
	public $App = "";

	public function checkApiToken(){
		if(!strlen($this->access_token)){
			$this->error="No API token youtube";
		}
	}

	public function getFormatResult($method, $part, $playlist = '', $channel = false){

		if($playlist) {
			$urlEnd = '&playlistId='.$playlist;
		} else {
			$urlEnd = '&order='.$this->sort.'&type=video';
		}

		$url = self::URL_YOUTUBE_API.$method.'?key='.$this->access_token.'&channelId='.$this->channel_id.'&part='.$part.'&maxResults='.$this->count_post.$urlEnd;

		if($channel){
			$url = self::URL_YOUTUBE_API.$method.'?key='.$this->access_token.'&id='.$this->channel_id.'&part='.$part;
		}

		if(function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			$out = curl_exec($curl);
			$data =  $out ? $out : curl_error($curl);
		}
		else
		{
			$data = file_get_contents($url);
		}

		$data = json_decode($data, true);
		$data = $this->App->ConvertCharsetArray($data, 'UTF-8', LANG_CHARSET);

		return $data;
	}

	public function getYoutubeVideos(){
		$data=$this->getFormatResult('search', 'id');

		return $data;
	}

	public function getYoutubeVideosByPlaylist(){
		$data=$this->getFormatResult('playlistItems', 'snippet', $this->playlist_id);

		return $data;
	}

	public function getYoutubeChannelInfo(){

		$data=$this->getFormatResult('channels', 'snippet,statistics,brandingSettings', '', true, 'items/snippet/title,items/snippet/description,items/snippet/thumbnails/default,items/brandingSettings/image/bannerImageUrl,items/statistics/subscriberCount,items/statistics/videoCount,items/statistics/viewCount');

		return $data;
	}

	public function arResultSet(){
	
			include_once('functions.php');
			
			global $APPLICATION;
			$this->access_token = $this->arParams["API_TOKEN_YOUTUBE"];
			$this->channel_id = $this->arParams["CHANNEL_ID_YOUTUBE"];
			$this->sort = $this->arParams["SORT_YOUTUBE"];
			$this->playlist_id = $this->arParams["PLAYLIST_ID_YOUTUBE"];
			$this->count_post = $this->arParams["COUNT_VIDEO_YOUTUBE"];
			$this->App=$APPLICATION;

			if($this->playlist_id) {

				$arYoutubeVideos = $this->getYoutubeVideosByPlaylist();

				if($arYoutubeVideos['error']){
					$this->AbortResultCache();
					$arResult['ERRORS']['MESSAGE'] = $arYoutubeVideos['error']['errors'][0]['message'];
					$arResult['ERRORS']['REASON'] = $arYoutubeVideos['error']['errors'][0]['reason'];
				} else {
					foreach($arYoutubeVideos['items'] as $key => $video):
						$api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id='.$video['id']['videoId'].'&key='.$this->access_token;
						$dataVideo = json_decode(file_get_contents($api_url));

						if(!empty($dataVideo->items[0]->snippet->thumbnails->high)){
							$arResult['ITEMS'][$key]['IMAGE'] = $dataVideo->items[0]->snippet->thumbnails->high->url;
						} else {
							$arResult['ITEMS'][$key]['IMAGE'] = $dataVideo->items[0]->snippet->thumbnails->medium->url;
						}

						$arResult['ITEMS'][$key]['ID'] = $video['snippet']['resourceId']['videoId'];
					endforeach;
				}

			} else {

				$arYoutubeVideos = $this->getYoutubeVideos();

				if($arYoutubeVideos['error']){
					$this->AbortResultCache();
					$arResult['ERRORS']['MESSAGE'] = $arYoutubeVideos['error']['errors'][0]['message'];
					$arResult['ERRORS']['REASON'] = $arYoutubeVideos['error']['errors'][0]['reason'];
				} else {
					foreach($arYoutubeVideos['items'] as $key => $video):
						$api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id='.$video['id']['videoId'].'&key='.$this->access_token;
						$dataVideo = json_decode(file_get_contents($api_url));

						if(!empty($dataVideo->items[0]->snippet->thumbnails->high)){
							$arResult['ITEMS'][$key]['IMAGE'] = $dataVideo->items[0]->snippet->thumbnails->high->url;
						} else {
							$arResult['ITEMS'][$key]['IMAGE'] = $dataVideo->items[0]->snippet->thumbnails->medium->url;
						}
						$arResult['ITEMS'][$key]['ID'] = $video['id']['videoId'];
					endforeach;
				}

			}

			$arChannelInfo = $this->getYoutubeChannelInfo();

			if($arChannelInfo['error']){
				$this->AbortResultCache();
				$arResult['ERRORS']['MESSAGE'] = $arChannelInfo['error']['errors'][0]['message'];
				$arResult['ERRORS']['REASON'] = $arChannelInfo['error']['errors'][0]['reason'];
			} else {

				$arResult['CHANNEL_INFO']['BANNER'] = $arChannelInfo['items'][0]['brandingSettings']['image'];
				$arResult['CHANNEL_INFO']['TITLE'] = $arChannelInfo['items'][0]['snippet']['title'];
				$arResult['CHANNEL_INFO']['DESCRIPTION'] = $arChannelInfo['items'][0]['snippet']['description'];
				$arResult['CHANNEL_INFO']['ICON'] = $arChannelInfo['items'][0]['snippet']['thumbnails'];
				$arResult['CHANNEL_INFO']['SUBSCRIBERS'] = numberPrepare($arChannelInfo['items'][0]['statistics']['subscriberCount']);
				$arResult['CHANNEL_INFO']['VIDEO_COUNT'] = numberPrepare($arChannelInfo['items'][0]['statistics']['videoCount']);
				$arResult['CHANNEL_INFO']['VIEW_COUNT'] = numberPrepare($arChannelInfo['items'][0]['statistics']['viewCount']);
				$arResult['RIGHT_LINK'] = "https://www.youtube.com/channel/";

				$arResult['SUBSCRIBE_BUTTON'] = '<script src="https://apis.google.com/js/platform.js"></script><div class="g-ytsubscribe" data-channelid="'.$this->channel_id.'" data-layout="default" data-count="default"></div>';

			}

		return $arResult;
	}

    public function executeComponent()
    {
        if($this->startResultCache())
        {
            $this->arResult = $this->arResultSet();
            $this->includeComponentTemplate();
        }
        return $this->arResult;
    }
};
?>