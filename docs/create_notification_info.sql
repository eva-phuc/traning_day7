CREATE TABLE notification_info (
    user_id int(11) NOT NULL,
    is_mail_alert_deny tinyint(1) default 0 NOT NULL,
    os_type ENUM("ios","android") default NULL,
    device_token VARCHAR(128) default NULL,
    deleted_at datetime default NULL,
    created_at timestamp NOT NULL,
    updated_at timestamp NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

