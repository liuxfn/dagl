CREATE TABLE `user` (
`USER_ID`  int(10) NOT NULL AUTO_INCREMENT ,
`USER_NAME`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`USER_SF`  char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '用户身份:0 普通用户 1 管理员' ,
`SSXM`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`PASSWORD`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`LRRQ`  datetime NOT NULL ,
`XGRQ`  datetime NULL DEFAULT NULL ,
`YXBZ`  char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`BZ`  varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`USER_ID`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1001
ROW_FORMAT=DYNAMIC
;


insert into user values(1000,'超级管理员','1','总部','e10adc3949ba59abbe56e057f20f883e',now(),null,'Y',null);
