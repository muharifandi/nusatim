package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import retrofit2.http.*

interface MarketingApiService {

    @GET("marketing-materials")
    suspend fun getMarketingMaterials(
        @Query("category") category: String? = null
    ): Map<String, List<MarketingMaterialResponse>>

    @GET("marketing-materials/{id}")
    suspend fun getMarketingMaterialDetail(@Path("id") id: Int): BaseResponse<MarketingMaterialResponse>
}
