package com.nusatim.partner.features.profile.ui.support

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.databinding.FragmentSupportTicketsBinding
import com.nusatim.partner.features.profile.ui.support.state.SupportTicketsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class SupportTicketsFragment : BaseFragment<FragmentSupportTicketsBinding>() {

    private val viewModel: SupportTicketsViewModel by viewModels()
    private val adapter by lazy {
        SupportTicketsAdapter { ticket ->
            findNavController().navigate("partner://support/detail?id=${ticket.id}".toUri())
        }
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = "Support Ticket"
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.rvTickets.layoutManager = LinearLayoutManager(requireContext())
        binding.rvTickets.adapter = adapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(SupportTicketsIntent.LoadTickets())
        }

        binding.fabAdd.setOnClickListener {
            findNavController().navigate("partner://support/add".toUri())
        }
        
        binding.btnCreateEmpty.setOnClickListener {
            findNavController().navigate("partner://support/add".toUri())
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    adapter.submitList(state.ticketsResponse?.data)
                    
                    val isEmpty = state.ticketsResponse?.data.isNullOrEmpty() && !state.isLoading
                    binding.layoutEmpty.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    binding.rvTickets.visibility = if (isEmpty) android.view.View.GONE else android.view.View.VISIBLE
                }
            }
        }
    }
}
