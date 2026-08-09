package com.nusatim.partner.features.profile.ui.main

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.features.profile.domain.usecase.GetProfileUseCase
import com.nusatim.partner.features.profile.domain.usecase.LogoutUseCase
import com.nusatim.partner.features.profile.domain.usecase.UpdateKycUseCase
import com.nusatim.partner.features.profile.domain.usecase.UpdatePasswordUseCase
import com.nusatim.partner.features.profile.domain.usecase.UpdateProfileUseCase
import com.nusatim.partner.features.profile.ui.main.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import javax.inject.Inject

@HiltViewModel
class ProfileViewModel @Inject constructor(
    private val getProfileUseCase: GetProfileUseCase,
    private val logoutUseCase: LogoutUseCase,
    private val updateProfileUseCase: UpdateProfileUseCase,
    private val updateKycUseCase: UpdateKycUseCase,
    private val updatePasswordUseCase: UpdatePasswordUseCase,
    private val sessionManager: SessionManager
) : BaseViewModel<ProfileState, ProfileIntent, ProfileEffect>(ProfileState()) {

    init {
        processIntent(ProfileIntent.LoadProfile)
    }

    override fun processIntent(intent: ProfileIntent) {
        when (intent) {
            is ProfileIntent.LoadProfile -> loadProfile()
            is ProfileIntent.Logout -> logout()
            is ProfileIntent.UpdateProfile -> updateProfile(intent.data)
            is ProfileIntent.UpdatePhoto -> updateKyc(intent.file, "photo")
            is ProfileIntent.UpdateKtp -> updateKyc(intent.file, "ktp")
            is ProfileIntent.UpdateNpwp -> updateKyc(intent.file, "npwp")
            is ProfileIntent.UpdatePassword -> updatePassword(intent.current, intent.new, intent.confirm)
        }
    }

    private fun loadProfile() {
        viewModelScope.launch {
            getProfileUseCase().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        sessionManager.savePartnerName(result.data.name)
                        sessionManager.savePartnerPhoto(result.data.profilePhotoUrl)
                        copy(isLoading = false, partner = result.data, error = null) 
                    }
                    is ResultState.Error -> setState { 
                        copy(isLoading = false, error = result.message) 
                    }
                }
            }
        }
    }

    private fun updateProfile(data: Map<String, Any>) {
        viewModelScope.launch {
            updateProfileUseCase(data).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        sessionManager.savePartnerName(result.data.name)
                        sessionManager.savePartnerPhoto(result.data.profilePhotoUrl)
                        setState { copy(isLoading = false, partner = result.data, error = null) }
                        sendEffect { ProfileEffect.SuccessUpdate }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { ProfileEffect.ShowToast(result.message) }
                    }
                }
            }
        }
    }

    private fun updateKyc(file: java.io.File, type: String) {
        val requestFile = file.asRequestBody("image/*".toMediaTypeOrNull())
        val fieldName = when (type) {
            "photo" -> "profile_photo" // Matching the requirement in 01-auth.md
            "ktp" -> "ktp"
            "npwp" -> "npwp"
            else -> type
        }
        val body = MultipartBody.Part.createFormData(fieldName, file.name, requestFile)
        
        viewModelScope.launch {
            val flow: Flow<ResultState<PartnerResponse>> = when (type) {
                "photo" -> updateKycUseCase.updatePhoto(body)
                "ktp" -> updateKycUseCase.updateKtp(body)
                else -> updateKycUseCase.updateNpwp(body)
            }
            
            flow.collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        sessionManager.savePartnerName(result.data.name)
                        sessionManager.savePartnerPhoto(result.data.profilePhotoUrl)
                        setState { copy(isLoading = false, partner = result.data, error = null) }
                        sendEffect { ProfileEffect.SuccessUpdate }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { ProfileEffect.ShowToast(result.message) }
                    }
                }
            }
        }
    }

    private fun updatePassword(current: String, new: String, confirm: String) {
        viewModelScope.launch {
            updatePasswordUseCase(current, new, confirm).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { ProfileEffect.SuccessUpdate }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { ProfileEffect.ShowToast(result.message) }
                    }
                }
            }
        }
    }

    private fun logout() {
        viewModelScope.launch {
            logoutUseCase().collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { ProfileEffect.LogoutSuccess }
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { ProfileEffect.LogoutSuccess }
                    }
                }
            }
        }
    }
}
