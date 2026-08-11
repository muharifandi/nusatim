package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody

interface ProfileRepository {
    fun getProfile(): Flow<ResultState<PartnerResponse>>
    fun updateProfile(request: Map<String, Any>): Flow<ResultState<PartnerResponse>>
    fun updatePhoto(profilePhoto: MultipartBody.Part): Flow<ResultState<PartnerResponse>>
    fun updateKtp(ktp: MultipartBody.Part): Flow<ResultState<PartnerResponse>>
    fun updateNpwp(npwp: MultipartBody.Part): Flow<ResultState<PartnerResponse>>
    fun updatePassword(request: Map<String, String>): Flow<ResultState<String>>
    fun deleteAccount(): Flow<ResultState<String>>
}
