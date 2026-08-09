package com.nusatim.partner.features.home.ui.dashboard.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.DashboardResponse

data class DashboardState(
    val isLoading: Boolean = false,
    val dashboard: DashboardResponse? = null,
    val error: String? = null
) : UiState
