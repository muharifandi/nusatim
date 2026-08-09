package com.nusatim.partner.features.home.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.home.R

class DashboardRobot : BaseRobot() {
    fun verifyDashboardIsDisplayed() {
        viewIsDisplayed(R.id.swipe_refresh)
    }
}

fun dashboard(func: DashboardRobot.() -> Unit) = DashboardRobot().apply { func() }
