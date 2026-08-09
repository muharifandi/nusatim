package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.ProfileApiService
import com.nusatim.partner.core.model.dto.PartnerResponse
import kotlinx.coroutines.flow.Flow
import okhttp3.MultipartBody
import javax.inject.Inject

class ProfileRepositoryImpl @Inject constructor(
    private val apiService: ProfileApiService
) : BaseRepository(), ProfileRepository {

    override fun getProfile(): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.getProfile().data
    }

    override fun updateProfile(request: Map<String, Any>): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.updateProfile(request).data
    }

    override fun updatePhoto(profilePhoto: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.updatePhoto(profilePhoto).data
    }

    override fun updateKtp(ktp: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.updateKtp(ktp).data
    }

    override fun updateNpwp(npwp: MultipartBody.Part): Flow<ResultState<PartnerResponse>> = safeNetworkCall {
        apiService.updateNpwp(npwp).data
    }

    override fun updatePassword(request: Map<String, String>): Flow<ResultState<String>> = safeNetworkCall {
        apiService.updatePassword(request)["message"] ?: "Success"
    }
}
