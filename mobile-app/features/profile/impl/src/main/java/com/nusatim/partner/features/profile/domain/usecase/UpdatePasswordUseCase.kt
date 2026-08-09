package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class UpdatePasswordUseCase @Inject constructor(
    private val repository: ProfileRepository
) {
    operator fun invoke(current: String, new: String, confirm: String): Flow<ResultState<String>> {
        val request = mapOf(
            "current_password" to current,
            "password" to new,
            "password_confirmation" to confirm
        )
        return repository.updatePassword(request)
    }
}
