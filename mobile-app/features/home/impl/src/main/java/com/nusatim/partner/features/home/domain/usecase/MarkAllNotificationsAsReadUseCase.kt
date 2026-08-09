package com.nusatim.partner.features.home.domain.usecase

import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class MarkAllNotificationsAsReadUseCase @Inject constructor(
    private val repository: NotificationsRepository
) {
    operator fun invoke(): Flow<ResultState<String>> {
        return repository.markAllAsRead()
    }
}
