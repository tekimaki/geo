<?php
/**
* $Header: /cvsroot/bitweaver/_bit_geo/LibertyGeo.php,v 1.4 2006/08/11 17:56:24 wjames5 Exp $
* @date created 2006/08/01
* @author Will <will@onnyturf.com>
* @version $Revision: 1.4 $ $Date: 2006/08/11 17:56:24 $
* @class LibertyGeo
*/

require_once( KERNEL_PKG_PATH.'BitBase.php' );

class LibertyGeo extends LibertyBase {
	var $mContentId;

	function LibertyGeo( $pContentId=NULL ) {
		LibertyBase::LibertyBase();
		$this->mContentId = $pContentId;
	}

	/**
	* Load the data from the database
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	**/
	function load() {
		if( $this->isValid() ) {
			$query = "SELECT * FROM `".BIT_DB_PREFIX."geo` WHERE `content_id`=?";
			$this->mInfo = $this->mDb->getRow( $query, array( $this->mContentId ) );
		}
		return( count( $this->mInfo ) );
	}

	/**
	* @param array pParams hash of values that will be used to store the page
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."geo";
			$this->mDb->StartTrans();
			if( !empty( $this->mInfo ) ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['geo_store'], array( "content_id" => $this->mContentId ) );
			} else {
				$result = $this->mDb->associateInsert( $table, $pParamHash['geo_store'] );
			}
			$this->mDb->CompleteTrans();
			$this->load();
		}
		return( count( $this->mErrors )== 0 );
	}

	/**
	* Make sure the data is safe to store
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verify( &$pParamHash ) {
		$pParamHash['geo_store'] = array();
		if( $this->isValid() ) {
			$this->load();
			$pParamHash['geo_store']['content_id'] = $this->mContentId;
			if(!empty( $pParamHash['geo'])){			
			 if( ( !empty( $pParamHash['geo']['lat'] ) && is_numeric( $pParamHash['geo']['lat'] ) ) || $pParamHash['geo']['lat'] == 0 ) {
				  $pParamHash['geo_store']['lat'] = $pParamHash['geo']['lat'];
			 }
			 if( ( !empty( $pParamHash['geo']['lng'] ) && is_numeric( $pParamHash['geo']['lng'] ) ) || $pParamHash['geo']['lng'] == 0 ) {
				  $pParamHash['geo_store']['lng'] = $pParamHash['geo']['lng'];
			 }
			 if( ( !empty( $pParamHash['geo']['amsl'] ) && is_numeric( $pParamHash['geo']['amsl'] ) ) || $pParamHash['geo']['amsl'] == 0 ) {
				$pParamHash['geo_store']['amsl'] = $pParamHash['geo']['amsl'];
			 }
			 if( !empty( $pParamHash['geo']['amsl_unit'] ) ) {
		  		$pParamHash['geo_store']['amsl_unit'] = $pParamHash['geo']['amsl_unit'];
			 }
			}
		}
		return( count( $this->mErrors )== 0 );
	}

	/**
	* check if the mContentId is set and valid
	*/
	function isValid() {
		return( @BitBase::verifyId( $this->mContentId ) );
	}

	/**
	* This function removes a geo entry
	**/
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."geo` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
		}
		return $ret;
	}
}

/********* SERVICE FUNCTIONS *********/

function geo_content_load_sql() {
	global $gBitSystem;
	$ret = array();
	$ret['select_sql'] = " , geo.* ";
	$ret['join_sql'] = " LEFT JOIN `".BIT_DB_PREFIX."geo` geo ON ( lc.`content_id`=geo.`content_id` )";
	return $ret;
}

function geo_content_store( &$pObject, &$pParamHash ) {
	global $gBitSystem;
	$errors = NULL;
	// If a content access system is active, let's call it
	if( $gBitSystem->isPackageActive( 'geo' ) ) {
		$geo = new LibertyGeo( $pObject->mContentId );
		if ( !$geo->store( $pParamHash ) ) {
			$errors['geo'] = $geo->mErrors['geo'];
		}
	}
	return( $errors );
}

function geo_content_expunge( &$pObject ) {
	$geo = new LibertyGeo( $pObject->mContentId );
	$geo->expunge();
}
?>
