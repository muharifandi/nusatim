package com.nusatim.partner.features.finance.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.finance.R

class FinanceRobot : BaseRobot() {
    fun verifyCommissionsListIsDisplayed() = viewIsDisplayed(R.id.rv_commissions)
    fun verifyWithdrawalsListIsDisplayed() = viewIsDisplayed(R.id.rv_withdrawals)
    fun clickRequestWithdrawal() = clickButton(R.id.fab_request)
}

fun finance(func: FinanceRobot.() -> Unit) = FinanceRobot().apply { func() }
