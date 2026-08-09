package com.nusatim.partner.features.home.ui.dashboard.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface DashboardIntent : UiIntent {
    data object LoadDashboard : DashboardIntent
    data object RefreshDashboard : DashboardIntent
}
