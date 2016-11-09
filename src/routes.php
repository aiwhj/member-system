<?php
//Members操作
$app->get('/','Index:Index')->setName('Index');
$app->group('/members', function () use ($app) {
	$app->get('/login','Members:MembersLogin')->setName('MembersLogin'); //登录
	$app->get('/reg','\Members:MembersReg')->setName('MembersReg'); //注册
	$app->post('/login', '\Members:MembersLoginDo')->setName('MembersLoginDo'); //登陆处理
	$app->post('/reg', '\Members:MembersRegDo')->setName('MembersRegDo'); //注册处理
});
$app->group('/personal', function () use ($app) {
	$app->get('/logout','Personal:PersonalLogout')->setName('PersonalLogout'); //退出登陆
	$app->get('/pass','Personal:PassUpdate')->setName('PassUpdate'); //退出登陆
	$app->get('/log','Personal:LoginLog')->setName('LoginLog'); //退出登陆
	$app->get('/personalinfo','\Personal:PersonalInfo')->setName('PersonalInfo'); //用户信息
	$app->put('/personalinfo', '\Personal:PersonalInfoUpdate')->setName('PersonalInfoUpdate'); //用户更新信息
	$app->delete('/personalinfo', '\Personal:PersonalInfoDelete')->setName('PersonalInfoDelete'); //删除用户
});
$app->group('/message', function () use ($app) {
	$app->post('/message', '\Message:MessageAdd')->setName('MessageAdd');
	$app->get('/messages', '\Message:Messages')->setName('Messages');
	$app->get('/message/{topic_id}', '\Message:MessageInfo')->setName('MessageInfo');
	$app->post('/message/{topic_id}', '\Message:MessageReply')->setName('MessageReply');
	$app->delete('/message/{id}', '\Message:MessageDelete')->setName('MessageDelete');
});
$app->group('/announcement', function () use ($app) {
	$app->get('/announcements', '\Announcement:Announcements')->setName('Announcements');
	$app->get('/announcement/{id}', '\Announcement:AnnouncementInfo')->setName('AnnouncementInfo');
});
$app->group('/team', function () use ($app) {
	$app->get('/teamstree', '\Team:TeamsTree')->setName('TeamsTree');
	$app->get('/teamslist', '\Team:TeamsList')->setName('TeamsList');
	$app->get('/getteamslist/{id}', '\Team:GetTeamsList')->setName('GetTeamsList');
	$app->post('/getteamslistasync', '\Team:GetTeamsListAsync')->setName('GetTeamsListAsync');
	$app->post('/teamoneinfo', '\Team:TeamOneInfo')->setName('TeamOneInfo');
});
$app->group('/grade', function () use ($app) {
	$app->get('/grade', '\Grade:Graded')->setName('Graded');
});
$app->group('/order', function () use ($app) {
	$app->get('/orderinfo', '\Order:OrderInfo')->setName('OrderInfo');
});
//公共路由
$app->group('/common', function () use ($app) {
	$app->get('/captcha', '\Common:Captcha')->setName('Captcha');
	$app->get('/getregions/{id}', '\Common:GetRegions')->setName('GetRegions');
	$app->post('/verity', '\Common:Verity')->setName('GetVerity');
	$app->get('/regionname/{id}', '\Common:GetRegionName')->setName('GetRegionName');
});
//提现
$app->group('/cash', function () use ($app) {
	$app->post('/cash', '\Cash:ToCash')->setName('ToCash');
	$app->get('/cashs', '\Cash:Cashs')->setName('Cashs');
	$app->get('/cash/{id}', '\Cash:GetCash')->setName('GetCash');
});
//充值
$app->group('/incharge', function () use ($app) {
	$app->post('/incharge', '\Incharge:ToIncharge')->setName('ToIncharge');
	$app->get('/incharges', '\Incharge:Incharges')->setName('Incharges');
	$app->get('/incharge/{id}', '\Incharge:GetIncharge')->setName('GetIncharge');
});
//转账
$app->group('/transfer', function () use ($app) {
	$app->post('/transfer', '\Transfer:ToTransfer')->setName('ToTransfer');
	$app->get('/transfers', '\Transfer:Transfers')->setName('Transfers');
	$app->get('/transfer/{id}', '\Transfer:GetTransfer')->setName('GetTransfer');
});
//管理
$app->group('/system', function () use ($app) {
	$app->get('','IndexAdmin:IndexAdmin')->setName('IndexAdmin');
	$app->get('/','IndexAdmin:IndexAdmin')->setName('IndexAdmin');
	$app->group('/admin', function () use ($app) {
		$app->get('/login', '\Login:AdminsLogin')->setName('AdminsLogin');
		$app->get('/logout', '\Login:AdminsLogout')->setName('AdminsLogout');
		$app->get('/reg','\Admins:AdminsReg')->setName('AdminsReg'); //注册
		$app->get('/loginlog','\Admins:AdminsLoginLog')->setName('AdminsLoginLog'); //注册
		$app->get('/admins','\Admins:AdminsList')->setName('AdminsList'); //注册
		$app->get('/roleadd','\Admins:AdminsRoleAdd')->setName('AdminsRoleAdd'); //注册
		$app->get('/authrule','\Admins:AdminsAuthRule')->setName('AdminsAuthRule'); //注册
		$app->get('/adminsinfo','\Admins:AdminsInfo')->setName('AdminsInfo'); //用户信息
		$app->post('/login', '\Login:AdminsLoginDo')->setName('AdminsLoginDo'); //登陆处理
		$app->post('/reg', '\Admins:AdminsRegDo')->setName('AdminsRegDo'); //注册处理
		$app->post('/roleadd', '\Admins:AdminsRoleAddDo')->setName('AdminsRoleAddDo'); //注册处理
		$app->get('/pass','Admins:PassUpdate')->setName('PassUpdateAdmin');
		$app->put('/adminsinfo', '\Admins:AdminsInfoUpdate')->setName('AdminsInfoUpdate'); //用户更新信息
		$app->delete('/adminsinfo/{id}', '\Admins:AdminsInfoDelete')->setName('AdminsInfoDelete'); //删除用户
		$app->delete('/rolesinfo/{id}', '\Admins:RolesDelete')->setName('RolesDelete'); //删除用户
	});
	$app->group('/incharge', function () use ($app) {
		$app->put('/incharge/{id}', '\InchargeAdmin:CheckIncharge')->setName('CheckInchargeAdmin');
		$app->get('/incharges', '\InchargeAdmin:Incharges')->setName('InchargesAdmin');
		$app->get('/noincharges', '\InchargeAdmin:NoIncharges')->setName('NoInchargesAdmin');
		$app->get('/incharge/{id}', '\InchargeAdmin:GetIncharge')->setName('GetInchargeAdmin');
	});
	$app->group('/announcement', function () use ($app) {
		$app->post('/announcement', '\AnnouncementAdmin:AnnouncementAdd')->setName('AnnouncementAddAdmin');
		$app->get('/announcements', '\AnnouncementAdmin:Announcements')->setName('AnnouncementsAdmin');
		$app->post('/announcementone', '\AnnouncementAdmin:Announcement')->setName('AnnouncementAdmin');
		$app->post('/announcement/{id}', '\AnnouncementAdmin:AnnouncementUpdate')->setName('AnnouncementUpdateAdmin');
		$app->delete('/announcement/{id}', '\AnnouncementAdmin:AnnouncementDelete')->setName('AnnouncementDeleteAdmin');
	});
	$app->group('/message', function () use ($app) {
		$app->post('/message', '\MessageAdmin:MessageAdd')->setName('MessageAddAdmin');
		$app->get('/messages', '\MessageAdmin:Messages')->setName('MessagesAdmin');
		$app->get('/message/{topic_id}', '\MessageAdmin:MessageInfo')->setName('MessageInfoAdmin');
		$app->post('/message/{topic_id}', '\MessageAdmin:MessageReply')->setName('MessageReplyAdmin');
		$app->delete('/message/{id}', '\MessageAdmin:MessageDelete')->setName('MessageDeleteAdmin');
	});
	$app->group('/transfer', function () use ($app) {
		$app->get('/transfers', '\TransferAdmin:Transfers')->setName('TransfersAdmin');
		$app->get('/transfer/{id}', '\TransferAdmin:GetTransfer')->setName('GetTransferAdmin');
	});
	$app->group('/matter', function () use ($app) {
		$app->get('/matter', '\MatterAdmin:Matter')->setName('Matter');
	});
	$app->group('/cash', function () use ($app) {
		$app->put('/cash/{id}', '\CashAdmin:CheckCash')->setName('CheckCashAdmin');
		$app->get('/cashs', '\CashAdmin:Cashs')->setName('CashsAdmin');
		$app->get('/ncashs', '\CashAdmin:NoCashs')->setName('NoCashsAdmin');
		$app->get('/cash/{id}', '\CashAdmin:GetCash')->setName('GetCashAdmin');
	});
	$app->group('/config', function () use ($app) {
		$app->get('/config', '\ConfigAdmin:ConfigIndex')->setName('ConfigIndexAdmin');
		$app->post('/reward', '\ConfigAdmin:RewardConfig')->setName('RewardConfigAdmin');
		$app->post('/cash', '\ConfigAdmin:CashConfig')->setName('CashConfigAdmin');
		$app->post('/transfer', '\ConfigAdmin:TransferConfig')->setName('TransferConfigAdmin');
		$app->post('/flow', '\ConfigAdmin:FlowConfig')->setName('FlowConfigAdmin');
		$app->post('/grade', '\ConfigAdmin:GradeConfig')->setName('GradeConfigAdmin');
		$app->post('/prepay', '\ConfigAdmin:PrepayConfig')->setName('PrepayConfigAdmin');
	});
	$app->group('/member', function () use ($app) {
		$app->post('/member/{id}', '\MembersAdmin:CheckMember')->setName('CheckMemberAdmin');
		$app->get('/members', '\MembersAdmin:Members')->setName('Members');
		$app->post('/getmemberlistasync', '\MembersAdmin:GetMemberListAsync')->setName('GetMemberListAsync');
		$app->get('/getmemberlist/{id}', '\MembersAdmin:GetMemberList')->setName('GetMemberList');
		$app->post('/member', '\MembersAdmin:Member')->setName('Member');
		$app->put('/member/{id}', '\MembersAdmin:MemberUpdate')->setName('MemberUpdateAdmin');
		$app->get('/grade', '\MembersAdmin:Grade')->setName('Grade');
		$app->get('/reward', '\MembersAdmin:Reward')->setName('Reward');
		$app->get('/memberstree', '\MembersAdmin:MembersTree')->setName('MembersTree');
		$app->get('/membersnc', '\MembersAdmin:MembersNCheck')->setName('MembersNCheck');
	});
	$app->group('/order', function () use ($app) {
		$app->get('/order', '\OrderAdmin:OrderInfo')->setName('OrderAdmin');
	});
	$app->group('/cartype', function () use ($app) {
		$app->get('/cartype', '\CartypeAdmin:Cartypes')->setName('Cartypes');
		$app->post('/cartype', '\CartypeAdmin:CartypeAdd')->setName('CartypeAdd');
		$app->delete('/cartype/{id}', '\CartypeAdmin:CartypeDelete')->setName('CartypeDelete');
	});
	$app->group('/prepay', function () use ($app) {
		$app->get('/prepay', '\PrepayAdmin:Prepays')->setName('Prepays');
	});

});
//导入导出
$app->group('/excel', function () use ($app) {
	$app->get('/excel', '\Excel:Excel')->setName('Excel');
});
//测试用
$app->group('/test', function () use ($app) {
	$app->get('/test', '\Tests:Test')->setName('Test');
});
