package com.nusatim.partner.features.profile.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.profile.R

class ProfileRobot : BaseRobot() {
    fun verifyName(name: String) = viewHasText(R.id.tv_name, name)
    fun verifyEmail(email: String) = viewHasText(R.id.tv_email, email)
    fun clickEditProfile() = clickButton(R.id.btn_edit_profile)
    fun clickLogout() = clickButton(R.id.btn_logout)
}

fun profile(func: ProfileRobot.() -> Unit) = ProfileRobot().apply { func() }
