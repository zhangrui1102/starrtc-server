<?php


function create_chat_room($userId, $roomId, $roomName, $roomType, $type, $conCurrentNumber){
	global $g_writeMdb;		
	$ctime = date('Y-m-d H:i:s'); 	
	
	try{	
		$sql = "insert into chatRoom (userId, roomId, roomName, roomType, type, conCurrentNumber, ctime, lastOnlineTime) values (?,?,?,?,?,?,?,?)";
		if(!($pstmt = $g_writeMdb->prepare($sql))){           
			return 13;    
		} 
		
		if($pstmt->execute(array($userId, $roomId, $roomName, $roomType, $type, $conCurrentNumber, $ctime, time()))){		
			return 0;
		}else{			
			return 14;
		}	
	}catch(PDOException $e){
		return 11;
	}	
	return 10;
}
//belongId 



function delete_chat_room($userId, $roomId){
	global $g_writeMdb;	
	try{			
		$sql = "select id from chatRoom where userId = ? and roomId = ? limit 1";
		if(!($pstmt = $g_writeMdb->prepare($sql))){           
            return 13;    
        }
		if($pstmt->execute(array($userId, $roomId))){
			$result = $pstmt->fetchAll();        
			$resNum = count($result);
			if($resNum == 0){
				return 0;//聊天室不存在,默认删除成功
			}						
			$id  = intval($result[0][0]);		
			
			$sql = "delete from `chatRoom` where `id` = ? limit 1";						
			if(!($pstmt = $g_writeMdb->prepare($sql))){           
				return 16;    
			}
			if($pstmt->execute(array($id))){			
				return 0;	
			}else{
				return 17;//删除失败
			}	
		}else{
			return 14;
		} 				   
	}catch(PDOException $e){		
		return 11;
	}	
	return 10;
}



function isRoomIdExist($roomId){ 	
	global $g_writeMdb;			
	$retArr = array();
    try{	
		$sql = "select userId, conCurrentNumber from chatRoom where roomId = ? limit 1";	
        if(!($pstmt = $g_writeMdb->prepare($sql))){           
            $retArr['ret'] = 12;return $retArr; 
        }			
		if($pstmt->execute(array($roomId))){	
			$result = $pstmt->fetchAll();        
			$resNum = count($result);
			if($resNum == 0){
				$retArr['ret'] = 14;return $retArr;//不存在
			}			
			
			$item = array();
			$item['userId'] 		  = $result[0][0];
			$item['conCurrentNumber'] = $result[0][1];
			$retArr['ret']  = 0;//存在
			$retArr['data'] = $item;	
			return $retArr;					
		}else{
			$retArr['ret'] = 13;return $retArr;
		}	       		
    }catch(PDOException $e){
		$retArr['ret'] = 11;return $retArr;
    } 	
	$retArr['ret'] = 10;return $retArr;	
}



//更新chatroom类型
function update_chatroom_type($roomId, $type){
	global $g_writeMdb;			
	try{
		$sql = "update chatRoom set type = ? where roomId = ? limit 1";
		if(!($pstmt = $g_writeMdb->prepare($sql))){ 
			return 12;
		}   
		if($pstmt->execute(array($type, $roomId))){
			return 0;	
		}else{
			return 13;
		}		
	}catch(PDOException $e){
		return 11;
	}	
    return 10;
}



function get_chatroom_list($listTypes){
	global $g_writeMdb;			
	$retArr = array();	
	try{			
		$sql = sprintf("select roomId, userId, roomName from chatRoom where type in (%s) order by id desc", $listTypes);	
		if(!($pstmt = $g_writeMdb->prepare($sql))){
            $retArr['ret'] = 12;return $retArr;    
        } 
		if($pstmt->execute()){
			$result = $pstmt->fetchAll();        
			$resNum = count($result);	
			
			
			$index  = 0;
			
			$roomIdArr  = array();
			$creatorArr = array();
			$nameArr = array();
			
			for($i = 0; $i < $resNum; $i++){			
				$roomId   = $result[$i][0];				
				$userId   = $result[$i][1];				
				$roomName = $result[$i][2];					
				
				
				$roomIdArr[$index]  = $roomId;
				$creatorArr[$index] = $userId;
				$nameArr[$index] =   $roomName;			
				$index++;
			}		
			
			$data = array();
			
			if(!empty($roomIdArr)){
				$data['roomIdList'] 		= implode(',', $roomIdArr);
			}			
			if(!empty($creatorArr)){
				$data['creatorList'] 		= implode(',', $creatorArr);
			}
			if(!empty($nameArr)){
				$data['userDefineDataList'] = implode(',', $nameArr);
			}
			
				
						
			$retArr['ret']  = 0;
			$retArr['data'] = $data;					 
			return $retArr;					
		}else{			
			$retArr['ret'] = 13;return $retArr;	
		}    
	}catch(PDOException $e){	
		$retArr['ret'] = 11;return $retArr;	
	}
	$retArr['ret'] = 10;return $retArr;	
}



function get_room_info($roomId){
	global $g_writeMdb;			
    $retArr = array();
    try{  
        $sql = "select id, userId from chatRoom where roomId = ? limit 1";
        if(!($pstmt = $g_writeMdb->prepare($sql))){
            $retArr['ret'] = 12;return $retArr;
        }
        if($pstmt->execute(array($roomId))){
            $result = $pstmt->fetchAll();
            $resNum = count($result);
            if($resNum == 0){
                $retArr['ret']  = 14; return $retArr;
            }
			
            $info = array();
            $info['id']       = $result[0][0];
            $info['userId']   = $result[0][1];           
            $retArr['ret']  = 0;
            $retArr['data'] = $info;
            return $retArr;
        }else{
            $retArr['ret'] = 13;return $retArr;
        }
    }catch(PDOException $e){
        $retArr['ret'] = 11;return $retArr;
    }
    $retArr['ret'] = 10;return $retArr;
}