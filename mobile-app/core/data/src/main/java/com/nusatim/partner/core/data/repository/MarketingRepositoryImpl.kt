package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.MarketingRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.MarketingApiService
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class MarketingRepositoryImpl @Inject constructor(
    private val apiService: MarketingApiService
) : BaseRepository(), MarketingRepository {

    override fun getMarketingMaterials(
        category: String?
    ): Flow<ResultState<Map<String, List<MarketingMaterialResponse>>>> = safeNetworkCall {
        apiService.getMarketingMaterials(category)
    }

    override fun getMarketingMaterialDetail(id: Int): Flow<ResultState<MarketingMaterialResponse>> = safeNetworkCall {
        apiService.getMarketingMaterialDetail(id).data
    }
}
