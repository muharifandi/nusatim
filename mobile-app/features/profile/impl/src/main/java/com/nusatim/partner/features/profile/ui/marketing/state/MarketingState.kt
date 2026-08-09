package com.nusatim.partner.features.profile.ui.marketing.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse

data class MarketingState(
    val isLoading: Boolean = false,
    val materials: Map<String, List<MarketingMaterialResponse>> = emptyMap(),
    val selectedDetail: MarketingMaterialResponse? = null,
    val error: String? = null
) : UiState
