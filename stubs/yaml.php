<?php

const YAML_ANY_SCALAR_STYLE = 0;
const YAML_PLAIN_SCALAR_STYLE = 0;
const YAML_SINGLE_QUOTED_SCALAR_STYLE = 0;
const YAML_DOUBLE_QUOTED_SCALAR_STYLE = 0;
const YAML_LITERAL_SCALAR_STYLE = 0;
const YAML_FOLDED_SCALAR_STYLE = 0;
const YAML_NULL_TAG = "tag:yaml.org,2002:null";
const YAML_BOOL_TAG  = "tag:yaml.org,2002:bool";;
const YAML_STR_TAG  = "tag:yaml.org,2002:str";;
const YAML_INT_TAG  = "tag:yaml.org,2002:int";;
const YAML_FLOAT_TAG  = "tag:yaml.org,2002:float";;
const YAML_TIMESTAMP_TAG  = "tag:yaml.org,2002:timestamp";
const YAML_SEQ_TAG  = "tag:yaml.org,2002:seq";
const YAML_MAP_TAG = "tag:yaml.org,2002:map";;
const YAML_PHP_TAG = "!php/object";
const YAML_ANY_ENCODING = 0;
const YAML_UTF8_ENCODING = 0;
const YAML_UTF16LE_ENCODING = 0;
const YAML_UTF16BE_ENCODING = 0;
const YAML_ANY_BREAK = 0;
const YAML_CR_BREAK = 0;
const YAML_LN_BREAK = 0;
const YAML_CRLN_BREAK = 0;
/**
 * @param $yaml
 * @param int $pos
 * @param null $ndocs
 * @param array $callbacks
 * @return array
 */
function yaml_parse($yaml, $pos = 0, &$ndocs = null, array $callbacks = []){}

/**
 * @param $file
 * @param int $pos
 * @param null $ndocs
 * @param array $callbacks
 * @return array
 */
function yaml_parse_file($file, $pos = 0, &$ndocs = null, array $callbacks = []){}

/**
 * @param $url
 * @param int $pos
 * @param null $ndocs
 * @param array $callbacks
 * @return array
 */
function yaml_parse_url($url, $pos = 0, &$ndocs = null, array $callbacks = []){}

/**
 * @param $data
 * @param int $encoding
 * @param int $linebreak
 * @param array $callbacks
 * @return string
 */
function yaml_emit($data, $encoding = YAML_ANY_ENCODING, $linebreak = YAML_ANY_BREAK, array $callbacks = []){}

/**
 * @param $file
 * @param $data
 * @param int $encoding
 * @param int $linebreak
 * @param array $callbacks
 * @return bool
 */
function yaml_emit_file($file, $data, $encoding = YAML_ANY_ENCODING, $linebreak = YAML_ANY_BREAK, array $callbacks = []){}
