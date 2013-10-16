
-- create NetRom-user for CMS
INSERT INTO `user` SET `user`='netrom', voornaam='NetRom', achternaam='Test', email='chalet@netrom.ro', password='_9db77c04e1bc6630c2bbc99d787aab07030b4b08', userlevel=1, priv='22,24,17,14,16,11,12,15,13,7,29,3,9,1,5,25,21,6,23,27,28,30,2,10,26,8';

-- Add 'TEST' to user 'webtastic'
UPDATE `user` SET voornaam='Jeroen TEST' WHERE user_id=1;
