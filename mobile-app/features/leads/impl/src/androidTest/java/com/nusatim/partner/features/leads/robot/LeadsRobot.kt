package com.nusatim.partner.features.leads.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.leads.R

class LeadsRobot : BaseRobot() {
    fun verifyLeadsListIsDisplayed() {
        viewIsDisplayed(R.id.rv_leads)
    }
}

fun leads(func: LeadsRobot.() -> Unit) = LeadsRobot().apply { func() }
