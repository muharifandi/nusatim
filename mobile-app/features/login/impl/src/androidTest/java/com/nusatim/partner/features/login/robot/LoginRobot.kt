package com.nusatim.partner.features.login.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.login.R

class LoginRobot : BaseRobot() {
    fun setEmail(email: String) = fillEditText(R.id.et_email, email)
    fun setPassword(password: String) = fillEditText(R.id.et_password, password)
    fun clickLogin() = clickButton(R.id.btn_login)
    fun verifyLoginIsDisplayed() = viewIsDisplayed(R.id.btn_login)
}

fun login(func: LoginRobot.() -> Unit) = LoginRobot().apply { func() }
