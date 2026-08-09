package com.nusatim.partner.features.home.ui.dashboard.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface DashboardEffect : UiEffect {
    data class ShowToast(val message: String) : DashboardEffect
}
