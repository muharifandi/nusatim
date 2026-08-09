package com.nusatim.partner.features.profile.ui.marketing.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface MarketingEffect : UiEffect {
    data class ShowError(val message: String) : MarketingEffect
}
