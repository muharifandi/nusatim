package com.nusatim.partner.features.home.ui.notifications

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.home.databinding.FragmentNotificationsBinding
import com.nusatim.partner.features.home.ui.notifications.state.NotificationsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class NotificationsFragment : BaseFragment<FragmentNotificationsBinding>() {

    private val viewModel: NotificationsViewModel by viewModels()
    private val adapter by lazy {
        NotificationsAdapter { notification ->
            if (notification.readAt == null) {
                viewModel.processIntent(NotificationsIntent.MarkAsRead(notification.id))
            }
            // Navigasi kontekstual belum didukung API, cukup tandai dibaca
        }
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.home.R.string.notifications_title)
            setNavigationOnClickListener {
                requireActivity().onBackPressedDispatcher.onBackPressed()
            }
        }

        binding.rvNotifications.layoutManager = LinearLayoutManager(requireContext())
        binding.rvNotifications.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(NotificationsIntent.LoadNotifications())
        }
        
        viewModel.processIntent(NotificationsIntent.LoadNotifications())
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    adapter.submitList(state.notificationsResponse?.data)
                    
                    val isEmpty = state.notificationsResponse?.data.isNullOrEmpty() && !state.isLoading
                    binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    binding.rvNotifications.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                }
            }
        }
    }
}
