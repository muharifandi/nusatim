package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.core.net.toUri
import androidx.viewpager2.adapter.FragmentStateAdapter
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import kotlinx.coroutines.launch
import com.google.android.material.tabs.TabLayoutMediator
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadDetailBinding
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailEffect
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import androidx.navigation.fragment.findNavController

@AndroidEntryPoint
class LeadDetailFragment : BaseFragment<FragmentLeadDetailBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels()
    private val leadId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.leads.R.string.lead_detail_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.layoutToolbar.toolbar.inflateMenu(com.nusatim.partner.features.leads.R.menu.menu_lead_detail)
        binding.layoutToolbar.toolbar.setOnMenuItemClickListener { item ->
            when (item.itemId) {
                com.nusatim.partner.features.leads.R.id.action_edit -> {
                    findNavController().navigate("partner://leads/edit?id=$leadId".toUri())
                    true
                }
                com.nusatim.partner.features.leads.R.id.action_delete -> {
                    showDeleteConfirmation()
                    true
                }
                else -> false
            }
        }

        if (leadId != -1) {
            viewModel.loadLeadDetail(leadId)
        }

        setupViewPager()
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(androidx.lifecycle.Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        showLoading(state.isLoading)
                        state.lead?.let { lead ->
                            binding.layoutToolbar.toolbar.title = lead.name
                        }
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is LeadDetailEffect.Success -> {
                                findNavController().navigateUp()
                            }
                            is LeadDetailEffect.NavigateToCustomerDetail -> {
                                findNavController().navigate("partner://customers/detail?id=${effect.customerId}".toUri())
                            }
                            is LeadDetailEffect.ShowError -> {
                                // Show error toast
                            }
                        }
                    }
                }
            }
        }
    }

    private fun showDeleteConfirmation() {
        com.google.android.material.dialog.MaterialAlertDialogBuilder(requireContext())
            .setTitle("Hapus Lead")
            .setMessage("Apakah Anda yakin ingin menghapus lead ini?")
            .setPositiveButton("Hapus") { _, _ ->
                viewModel.processIntent(LeadDetailIntent.DeleteLead(leadId))
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun setupViewPager() {
        val adapter = object : FragmentStateAdapter(this) {
            override fun getItemCount(): Int = 4
            override fun createFragment(position: Int): Fragment {
                return when (position) {
                    0 -> LeadInfoFragment()
                    1 -> LeadReminderFragment()
                    2 -> LeadDocumentFragment()
                    else -> LeadTimelineFragment()
                }
            }
        }
        binding.viewPager.adapter = adapter

        TabLayoutMediator(binding.tabLayout, binding.viewPager) { tab, position ->
            tab.text = when (position) {
                0 -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_info)
                1 -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_reminder)
                2 -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_document)
                else -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_timeline)
            }
        }.attach()
    }
}
