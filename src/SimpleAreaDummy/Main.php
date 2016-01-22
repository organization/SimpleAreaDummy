<?php

namespace SimpleAreaDummy;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\scheduler\Task;
use ifteam\SimpleArea\database\minefarm\MineFarmManager;
use ifteam\SimpleArea\database\minefarm\MineFarmLoader;
use ifteam\SimpleArea\database\area\AreaProvider;
use ifteam\SimpleArea\database\area\AreaSection;
use pocketmine\Server;

class BuyTask extends Task {
	/** @var MineFarmLoader */
	public $mineFarmLoader;
	public function __construct(MineFarmLoader $mineFarmLoader) {
		$this->mineFarmLoader = $mineFarmLoader;
	}
	public function onRun($currentTick) {
		$name = $this->getRandomDummyName ();
		echo "$name 가 영역을 구매합니다.\n";
		$id = $this->mineFarmLoader->addMineFarm ( $name );
		$areaSection = AreaProvider::getInstance ()->getAreaToId ( 'minefarm', $id );
		
		if ($areaSection instanceof AreaSection) {
			if (mt_rand ( 0, 4 ) == 0)
				$areaSection->setOwner ( $this->getRandomDummyName () );
			
			$areaSection->setPvpAllow ( mt_rand ( 0, 1 ) );
			$areaSection->setHome ( mt_rand ( 0, 1 ) );
			$areaSection->setProtect ( mt_rand ( 0, 1 ) );
			$areaSection->setInvenSave ( mt_rand ( 0, 1 ) );
			$areaSection->setPrice ( mt_rand ( 0, 10000000 ) );
			$areaSection->setAccessDeny ( mt_rand ( 0, 1 ) );
			
			$messages = $this->mb_str_shuffle ( "가나다라마바사아자차카타파하아야어여오요우유으이1234567890!#$%^&*() ABCDEFGHIJKLMNOPQRSTUVWXYZ" );
			$messages = mb_convert_encoding ( $messages, "UTF-8" );
			
			echo '메시지: ' . $messages . "\n";
			$areaSection->setWelcome ( $messages );
			
			for($i = 0; $i <= mt_rand ( 1, 300 ); $i ++)
				$areaSection->setResident ( mt_rand ( 0, 1 ), $this->getRandomDummyName () );
		} else {
			Server::getInstance ()->getLogger ()->error ( "$buyer 가 $id 번 구매에 실패했습니다! [중요]" );
		}
	}
	public function getRandomDummyName() {
		$buyer = 'dummy';
		$buyer .= (mt_rand ( 0, 1 ) == 0) ? '가' : '나';
		$buyer .= (mt_rand ( 0, 1 ) == 0) ? '@' : '#';
		$buyer .= mt_rand ( 0, 10000 );
		return $buyer;
	}
	public function mb_str_shuffle($str) {
		$ret = array ();
		for($i = 0; $i < mb_strlen ( $str, "euc-kr" ); $i ++) {
			array_push ( $ret, mb_substr ( $str, $i, 1, "euc-kr" ) );
		}
		shuffle ( $ret );
		return join ( $ret );
	}
}
class Main extends PluginBase implements Listener {
	/** @var MineFarmLoader */
	public $mineFarmLoader;
	public function onEnable() {
		$this->initBase ();
		$this->getServer ()->getScheduler ()->scheduleRepeatingTask ( new BuyTask ( $this->mineFarmLoader ), 1 );
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	public function initBase() {
		$this->mineFarmLoader = $this->getPrivateVariableData ( MineFarmManager::getInstance (), 'mineFarmLoader' );
		echo $this->mineFarmLoader->createWorld () ? "월드 생성완료\n" : "월드 생성이미 되어있음\n";
	}
	public function getPrivateVariableData($object, $variableName) {
		$reflectionClass = new \ReflectionClass ( $object );
		$property = $reflectionClass->getProperty ( $variableName );
		$property->setAccessible ( true );
		return $property->getValue ( $object );
	}
}

?>