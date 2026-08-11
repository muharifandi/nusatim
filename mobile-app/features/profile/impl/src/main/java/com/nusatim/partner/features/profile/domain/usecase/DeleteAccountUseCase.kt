package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.onEach
import javax.inject.Inject

class DeleteAccountUseCase @Inject constructor(
    private val profileRepository: ProfileRepository,
    private val sessionManager: SessionManager
) {
    operator fun invoke(): Flow<ResultState<String>> {
        return profileRepository.deleteAccount().onEach { result ->
            if (result is ResultState.Success) {
                sessionManager.clearSession()
            }
        }
    }
}
