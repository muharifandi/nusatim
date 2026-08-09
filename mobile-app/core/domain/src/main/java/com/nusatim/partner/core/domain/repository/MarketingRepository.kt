package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import kotlinx.coroutines.flow.Flow

interface MarketingRepository {
    fun getMarketingMaterials(
        category: String? = null
    ): Flow<ResultState<Map<String, List<MarketingMaterialResponse>>>>

    fun getMarketingMaterialDetail(id: Int): Flow<ResultState<MarketingMaterialResponse>>
}
