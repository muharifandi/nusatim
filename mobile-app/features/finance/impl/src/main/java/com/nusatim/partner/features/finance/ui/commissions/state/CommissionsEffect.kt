package com.nusatim.partner.features.finance.ui.commissions.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface CommissionsEffect : UiEffect {
    data class ShowError(val message: String) : CommissionsEffect
}
