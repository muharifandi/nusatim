package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadTimelineBinding
import com.nusatim.partner.features.leads.ui.leads.detail.LeadActivityAdapter
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class CustomerTimelineFragment : BaseFragment<FragmentLeadTimelineBinding>() {

    private val viewModel: CustomerDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy { LeadActivityAdapter() }

    override fun onInitViews() {
        binding.rvActivities.layoutManager = LinearLayoutManager(requireContext())
        binding.rvActivities.adapter = adapter
        binding.fabAddNote.visibility = android.view.View.GONE // Read-only
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    adapter.submitList(state.customer?.timeline)
                }
            }
        }
    }
}
