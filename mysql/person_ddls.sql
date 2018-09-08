CREATE TABLE `person_ddls` (
`USER_ID`  int(10) NOT NULL ,
`USER_NAME`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`PERSON_ID`  int(10) NOT NULL ,
`XM`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`SSXM_BGQ`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`SSXM_BGH`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`LRRQ`  datetime NOT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=DYNAMIC
;