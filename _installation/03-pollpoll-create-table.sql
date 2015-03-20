CREATE TABLE IF NOT EXISTS `pollpoll`.`poll_event` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_type` tinyint NOT NULL,
  `start_time` timestamp NOT NULL,
  `end_time` timestamp NOT NULL,
  PRIMARY KEY (`event_id`),
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (user_id) REFERENCES `login`.`users` (user_id)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='poll event data';

CREATE TABLE IF NOT EXISTS `pollpoll`.`choice` (
  `choice_id` int PRIMARY KEY AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `image_url` varchar(255),
  `description` text,
  `vote_count` int,
  CONSTRAINT `fk_event_id`
    FOREIGN KEY (event_id) REFERENCES `pollpoll`.`poll_event` (event_id)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='choices can be voted for a event';

CREATE TABLE IF NOT EXISTS `pollpoll`.`voter` (
  `voter_id` int PRIMARY KEY AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `name` varchar(63) NOT NULL,
  `email` varchar(255) NOT NULL,
  `voted_choice_id` int,
  CONSTRAINT `fk_voter_event_id`
    FOREIGN KEY (event_id) REFERENCES `pollpoll`.`poll_event` (event_id)
    ON UPDATE RESTRICT
    ON DELETE CASCADE,
  CONSTRAINT `fk_choice_id`
    FOREIGN KEY (voted_choice_id) REFERENCES `pollpoll`.`choice` (choice_id)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='voter info';
