package com.nusatim.partner.features.register.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.register.R

class RegisterRobot : BaseRobot() {
    fun setName(name: String) = fillEditText(R.id.et_name, name)
    fun setEmail(email: String) = fillEditText(R.id.et_email, email)
    fun clickNext() = clickButton(R.id.btn_next)
    fun verifyRegisterIsDisplayed() = viewIsDisplayed(R.id.btn_next)
}

fun register(func: RegisterRobot.() -> Unit) = RegisterRobot().apply { func() }
