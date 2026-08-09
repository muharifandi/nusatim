package com.nusatim.partner.features.finance.ui.withdrawals.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface WithdrawalsEffect : UiEffect {
    data class ShowError(val message: String) : WithdrawalsEffect
    data object SuccessRequest : WithdrawalsEffect
}
