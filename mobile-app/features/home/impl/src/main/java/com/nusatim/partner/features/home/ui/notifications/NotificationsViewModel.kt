package com.nusatim.partner.features.home.ui.notifications

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.home.domain.usecase.*
import com.nusatim.partner.features.home.ui.notifications.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class NotificationsViewModel @Inject constructor(
    private val getNotificationsUseCase: GetNotificationsUseCase,
    private val getUnreadCountUseCase: GetUnreadNotificationsCountUseCase,
    private val markAsReadUseCase: MarkNotificationAsReadUseCase,
    private val markAllAsReadUseCase: MarkAllNotificationsAsReadUseCase
) : BaseViewModel<NotificationsState, NotificationsIntent, NotificationsEffect>(NotificationsState()) {

    init {
        loadUnreadCount()
    }

    override fun processIntent(intent: NotificationsIntent) {
        when (intent) {
            is NotificationsIntent.LoadNotifications -> loadNotifications(intent.page)
            is NotificationsIntent.LoadUnreadCount -> loadUnreadCount()
            is NotificationsIntent.MarkAsRead -> markAsRead(intent.id)
            is NotificationsIntent.MarkAllAsRead -> markAllAsRead()
        }
    }

    private fun loadNotifications(page: Int) {
        viewModelScope.launch {
            getNotificationsUseCase(page = page).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, notificationsResponse = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                    }
                }
            }
        }
    }

    private fun loadUnreadCount() {
        viewModelScope.launch {
            getUnreadCountUseCase().collectLatest { result ->
                if (result is ResultState.Success) {
                    setState { copy(unreadCount = result.data.unreadCount) }
                }
            }
        }
    }

    private fun markAsRead(id: String) {
        viewModelScope.launch {
            markAsReadUseCase(id).collectLatest { result ->
                if (result is ResultState.Success) {
                    loadUnreadCount()
                    // Optionally update local list state
                }
            }
        }
    }

    private fun markAllAsRead() {
        viewModelScope.launch {
            markAllAsReadUseCase().collectLatest { result ->
                if (result is ResultState.Success) {
                    setState { copy(unreadCount = 0) }
                    loadNotifications(1)
                }
            }
        }
    }
}
