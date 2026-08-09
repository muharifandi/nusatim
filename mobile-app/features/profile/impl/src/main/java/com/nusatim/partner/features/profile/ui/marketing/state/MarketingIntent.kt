package com.nusatim.partner.features.profile.ui.marketing.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface MarketingIntent : UiIntent {
    data class LoadMarketingMaterials(val category: String? = null) : MarketingIntent
    data class LoadMarketingMaterialDetail(val id: Int) : MarketingIntent
}
