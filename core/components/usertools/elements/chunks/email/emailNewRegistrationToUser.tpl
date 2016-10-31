<h1>Вы зарегистрировались на сайте [[++site_url]]</h1>
<p>Вы указали следующие реквизиты:</p>
<ul>
    [[+type:is=`fl`:then=`<li>Тип: физическое лицо</li>`:else=``]]
    [[+type:is=`ul`:then=`<li>Тип: юрлицо</li>`:else=``]]
    [[+firstname:notempty=`<li>имя: [[+firstname]]</li>`]]
    [[+lastname:notempty=`<li>фамилия: [[+lastname]]</li>`]]
    [[+company:notempty=`<li>компания: [[+company]]</li>`]]
    [[+inn:notempty=`<li>ИНН: [[+inn]]</li>`]]
    [[+kpp:notempty=`<li>КПП: [[+kpp]]</li>`]]
    [[+director_fullname:notempty=`<li>имя директора: [[+director_fullname]]</li>`]]
    [[+address:notempty=`<li>адрес: [[+address]]</li>`]]
    [[+website:notempty=`<li>веб-сайт: [[+website]]</li>`]]
    [[+phone:notempty=`<li>телефон: [[+phone]]</li>`]]
    [[+email:notempty=`<li>e-mail: [[+email]]</li>`]]
</ul>

<p>
    После того, как администратор активирует вашу учетную запись, вы получите уведомление с дальнейшими инструкциями.
</p>