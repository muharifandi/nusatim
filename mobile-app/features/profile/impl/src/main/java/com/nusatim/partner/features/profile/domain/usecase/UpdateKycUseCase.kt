package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import javax.inject.Inject

class UpdateKycUseCase @Inject constructor(
    private val repository: ProfileRepository
) {
    fun updatePhoto(file: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = repository.updatePhoto(file)
    fun updateKtp(file: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = repository.updateKtp(file)
    fun updateNpwp(file: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = repository.updateNpwp(file)
}
