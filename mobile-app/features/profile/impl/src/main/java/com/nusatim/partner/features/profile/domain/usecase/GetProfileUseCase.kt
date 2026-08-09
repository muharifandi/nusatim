package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetProfileUseCase @Inject constructor(
    private val repository: ProfileRepository
) {
    operator fun invoke(): Flow<ResultState<PartnerResponse>> = repository.getProfile()
}
