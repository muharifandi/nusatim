package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.MarketingRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetMarketingMaterialsUseCase @Inject constructor(
    private val repository: MarketingRepository
) {
    operator fun invoke(category: String? = null): Flow<ResultState<Map<String, List<MarketingMaterialResponse>>>> {
        return repository.getMarketingMaterials(category)
    }
}
