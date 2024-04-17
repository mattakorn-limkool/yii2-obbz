<?php

namespace obbz\yii2\extensions\google;

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use yii\base\BaseObject;

class Text2Speech extends BaseObject
{
    // default config
    public $defaultLanguage = 'th-Th';
    public $defaultAudioOutput = AudioEncoding::MP3;

    /** @var  VoiceSelectionParams */
    public $voiceSelectionParam;
    /** @var  AudioConfig */
    public $audioConfig;
    /** @var  SynthesisInput */
    public $input;


    public function init(){
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode($this->defaultAudioOutput);
        $this->voiceSelectionParam = $voice;

        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding($this->defaultAudioOutput);
        $this->audioConfig = $audioConfig;

        $this->input = new SynthesisInput();
    }

    public function getAudioContent($text){
        $textToSpeechClient = new TextToSpeechClient();
        $this->prepareText($text, $separatedTextArr);
        $audioContent = '';
        foreach($separatedTextArr as $key => $separatedText){
            $this->input->setText($separatedText);
            $resp = $textToSpeechClient->synthesizeSpeech($this->input, $this->voiceSelectionParam, $this->audioConfig);
            $audioContent .= $resp->getAudioContent();
        }

        return $audioContent;
    }


    public function prepareText($text, &$separatedText){
        $max = 5000;
        $separators = [" ", "\n", "\r"];
        if(strlen($text) >= $max){
            $scopeText = substr($text, 0, $max);
//        echo $scopeText; exit;
            $maxPos = 0;
            foreach($separators as $separator){
                $pos = strrpos($scopeText,$separator);
                if($pos !== false && $pos > $maxPos){
                    $maxPos = $pos;
                }
            }

            if($maxPos){
                $separatedText[] = substr($scopeText, 0, $maxPos);
                $prepareText = substr($text, $maxPos+1);
                return $this->prepareText($prepareText, $separatedText);
            }
        }

        $separatedText[] = $text;
        return $separatedText;
    }
}